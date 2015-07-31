<?php

#require_once('Persistence.php');

class Search {

    static private function split_terms($terms){

        $terms = preg_replace_callback("/\"(.*?)\"/", function($m) {return Search::transform_term($m[1]);}, $terms);
        $terms = preg_split("/\s+|,/", $terms);

        $out = array();

        foreach($terms as $term){

            $term = preg_replace_callback("/\{WHITESPACE-([0-9]+)\}/", function($m) {return chr($m[1]);}, $term);
            $term = preg_replace("/\{COMMA\}/", ",", $term);

            $out[] = $term;
        }
        
        return $out;
    }

    static function transform_term($term){
        $term = preg_replace_callback("/(\s)/", function($m) {return '{WHITESPACE-'.ord($m[1]).'}';}, $term);
        $term = preg_replace("/,/", "{COMMA}", $term);
        return $term;
    }
    
    static private function escape_rlike($string){
        return preg_replace("/([.\[\]*^\$])/", '\\\$1', $string);
    }
    
    static private function db_escape_terms($terms){
        $out = array();
        foreach($terms as $term){
            $out[] = '[[:<:]]'.AddSlashes(Search::escape_rlike($term)).'[[:>:]]';
        }
        return $out;
    }
    
    static function perform($terms){
        
        $terms = Search::split_terms($terms);
        $terms_db = Search::db_escape_terms($terms);
        $terms_rx = Search::rx_escape_terms($terms);
        
        $parts = array();
        foreach($terms_db as $term_db){
            array_push($parts, "(summary RLIKE '$term_db' OR title RLIKE '$term_db')");
        }
        $parts = implode(' AND ', $parts);
        
        $conn = Persistence::get_database_object();
        $sql = "SELECT id, title, summary, created  FROM postmortems WHERE $parts";
        $rows = array();
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        
        $timezone = getUserTimezone();
        $tz = new DateTimeZone($timezone);

        while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
                
            $row['score'] = 0;
            $row['created'] = new DateTime("@$row[created]");
            $row['created']->setTimezone($tz);
            $row['created'] = $row['created']->format('m/d/Y G:ia');
            
            foreach($terms_rx as $term_rx){
                $row['score'] += preg_match_all("/$term_rx/i", $row['summary'], $null);
                $row['score'] += preg_match_all("/$term_rx/i", $row['title'], $null);
            }
            
            $rows[] = $row;
        }
        
        uasort($rows, 'Search::sort_results');
        $conn = null;
        return $rows;
    }
    
    static private function rx_escape_terms($terms){
        $out = array();
        foreach($terms as $term){
            $out[] = '\b'.preg_quote($term, '/').'\b';
        }
        return $out;
    }
    
    static function sort_results($a, $b){
        
        $ax = $a['score'];
        $bx = $b['score'];
        
        if ($ax == $bx){ return 0; }
        return ($ax > $bx) ? -1 : 1;
    }
    
    static private function html_escape_terms($terms){
        $out = array();
        
        foreach($terms as $term){
            if (preg_match("/\s|,/", $term)){
                $out[] = '"'.HtmlSpecialChars($term).'"';
            }else{
                $out[] = HtmlSpecialChars($term);
            }
        }
        
        return $out;
    }
    
    static private function pretty_terms($terms_html){

        if (count($terms_html) == 1){
            return array_pop($terms_html);
        }
        
        $last = array_pop($terms_html);
        
        return implode(', ', $terms_html)." and $last";
    }
    
}
#
# do the search here...
#

#        $results = search_perform($HTTP_GET_VARS[q]);
#        $term_list = search_pretty_terms(search_html_escape_terms(search_split_terms($HTTP_GET_VARS[q])));

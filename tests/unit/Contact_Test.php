<?php

require_once(__DIR__."/../../features/contact/lib.php");

class ContactTest extends PHPUnit_Framework_TestCase {

    public function test_getUrlForUser_lookupDefined() {
        $config = array('lookup_url' => 'http://foo/%s/bar');
        $expected = 'http://foo/username/bar';
        $this->assertEquals($expected, Contact::get_url_for_user('username', $config));
    }

    public function test_getUrlForUser_nolookupDefined() {
        $config = array();
        $this->assertEquals(null, Contact::get_url_for_user('username', $config));
    }

    public function test_getHtmlForUser_lookupDefined() {
        $config = array('lookup_url' => 'http://foo/%s/bar');
        $expected = '<a href="http://foo/username/bar" target="_new">username</a>';
        $this->assertEquals($expected, Contact::get_html_for_user('username', $config));
    }

    public function test_getHtmlForUser_nolookupDefined() {
        $config = array();
        $expected = 'username';
        $this->assertEquals($expected, Contact::get_html_for_user('username', $config));
    }
}

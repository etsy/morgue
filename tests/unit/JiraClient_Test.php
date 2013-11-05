<?php

require_once(__DIR__."/../../features/jira/lib.php");
require_once(__DIR__."/../../phplib/CurlClient.php");
require_once(__DIR__."/../../phplib/Configuration.php");

class JiraClientTest extends PHPUnit_Framework_TestCase {
    const JIRA_BASE_URL = "https://jira.foo.com";
    const JIRA_USERNAME = 'jira';
    const JIRA_PASSWORD = 'credentials';

    public function setUp() {
        // create a mock curl client
        $this->curl_client = $this->getMock('CurlClient', array('get'));
        $this->jira_client = new JiraClient(
            $this->curl_client, array(
                "baseurl" => self::JIRA_BASE_URL,
                "username" => self::JIRA_USERNAME,
                "password" => self::JIRA_PASSWORD
            )
        );
    }

    public function testObjectCreation() {
        $curl_client = new CurlClient();
        $jira = new JiraClient($curl_client);
        $this->assertEquals('JiraClient', get_class($jira));
    }

    public function test_getJiraApiResponse_withTickets() {

        // set up expectations and return value
        $expected_url = self::JIRA_BASE_URL . '/rest/api/2/search';

        $expected_params = array(
            'jql' => 'issuekey in ("FOO-123","BAR-456","BAZ-7")',
            'maxResults' => 3,
            'fields' => 'x,y,z'
        );

        $expected_creds = self::JIRA_USERNAME . ':' . self::JIRA_PASSWORD;

        $this->curl_client
             ->expects($this->once())
             ->method('get')
             ->with($this->equalTo($expected_url), $this->equalTo($expected_params), $this->equalTo($expected_creds))
             ->will($this->returnValue('{"json": "response"}'));

        $fields = array('x', 'y' ,'z');
        $jira_response = $this->jira_client->getJiraApiResponse(array("FOO-123", "BAR-456", "BAZ-7"), $fields);
        $this->assertEquals(array('json' => 'response'), $jira_response);
    }

    public function test_getJiraApiResponse_withoutTickets() {
        // create a mock curl client
        $this->curl_client->expects($this->never())
            ->method('get');


        $fields = array('x', 'y' ,'z');
        $jira_response = $this->jira_client->getJiraApiResponse(array(), $fields);
        $this->assertEquals(array(), $jira_response);
    }


    public function provideTicketsFieldsRequirements() {
        return array(
            array( array('Foo' => 'foo'), array('Foo' => 'X')), # simple field
            array( array('BaZ' => 'baz'), array('BaZ' => 'Z')), # nested field
            array( array('Bar' => 'bar'), array('Bar' => '')), # field not found
            array( array('Foo' => 'foo', 'BaZ' => 'baz'), array('Foo' => 'X', 'BaZ' => 'Z')), # 2 fields
        );
    }

    /**
      * @dataProvider provideTicketsFieldsRequirements
      */
    public function test_unpackTicketInfo($fields, $expected) {
        $ticket_info = array(
            'key' => 'ABC',
            'fields' => array(
                'foo' => 'X',
                'bar' => array('qux'=>'Y'),
                'baz' => array('name'=>'Z'),
            )
        );
        $actual = $this->jira_client->unpackTicketInfo($ticket_info, $fields);
        $expected['ticket_url'] = self::JIRA_BASE_URL . '/browse/ABC';
        $this->assertEquals($expected, $actual);
    }

    public function test_unpackTicketInfo_emptyissue() {
        $ticket_info = array();
        $actual = $this->jira_client->unpackTicketInfo($ticket_info, array('Foo' => 'foo'));
        $this->assertEquals(array('Foo' => ''), $actual);
    }
}

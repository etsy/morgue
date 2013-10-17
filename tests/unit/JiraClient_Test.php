<?php

require_once(__DIR__."/../../features/jira/lib.php");
require_once(__DIR__."/../../phplib/CurlClient.php");
require_once(__DIR__."/../../phplib/Configuration.php");

class JiraClientTest extends PHPUnit_Framework_TestCase {

    public function testObjectCreation() {
        $curl_client = new CurlClient();
        $jira = new JiraClient($curl_client);
        $this->assertEquals('JiraClient', get_class($jira));
    }

    public function test_getJiraApiResponses() {
        // create a mock curl client
        $curl = $this->getMock('CurlClient', array('get'));
        // set up expectations and return value
        $curl->expects($this->once())
            ->method('get')
            ->with($this->equalTo('https://jira.foo.com/rest/api/2/issue/MAYHEM-1148'))
            ->will($this->returnValue(file_get_contents(__DIR__."/../fixtures/response1772.json")));

        $jira = new JiraClient($curl, array("baseurl" => "https://jira.foo.com", "username" => "foo", "password" => "bar"));
        $jira_responses = $jira->getJiraApiResponse(array('MAYHEM-1148'));
        $this->assertTrue(is_array($jira_responses));
        $this->assertEquals(1, count($jira_responses));
        $this->assertEquals("MAYHEM-1148", $jira_responses[0]["key"]);
        $this->assertEquals("bmacri", $jira_responses[0]['fields']['assignee']['name']);
    }
}

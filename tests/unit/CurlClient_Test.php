
<?php

require_once("phplib/CurlClient.php");

class CurlClientTest extends \PHPUnit\Framework\TestCase {

    public function setUp() {
        $this->curl_client = new CurlClient();
    }

    public function test_get() {

        $res = $this->curl_client->get("http://httpbin.org/get");
        $res = json_decode($res, true);
        $this->assertEquals("https://httpbin.org/get", $res["url"]);
    }

    public function test_getWithTimeout() {
        $res = $this->curl_client->get("https://httpbin.org/delay/3", null, null, null, 1);
        $res = json_decode($res);
        $this->assertNull($res);
    }

}

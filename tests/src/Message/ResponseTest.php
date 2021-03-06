<?php
declare (strict_types=1);

use PHPUnit\Framework\TestCase;
use AeonDigital\Http\Message\Response as Response;

require_once __DIR__ . "/../../phpunit.php";







class ResponseTest extends TestCase
{





    public function test_constructor_code_status_fail()
    {
        $headers    = prov_assocArray_to_Http_Header_01();
        $oHeaders   = prov_instanceOf_Http_HeaderCollection_01($headers);
        $oBody      = prov_instanceOf_Http_Stream_fromString("Test stream object");

        $fail = false;
        try {
            $res = new Response(99, "", "1.0", $oHeaders, $oBody);
        } catch (\Exception $ex) {
            $fail = true;
            $this->assertSame("Invalid value defined for \"statusCode\". Expected an integer between 100 and 599. Given: [ 99 ]", $ex->getMessage());
        }
        $this->assertTrue($fail, "Test must fail");
    }




    public function test_constructor_ok()
    {
        $headers    = prov_assocArray_to_Http_Header_01();
        $oHeaders   = prov_instanceOf_Http_HeaderCollection_01($headers);
        $oBody      = prov_instanceOf_Http_Stream_fromString("Test stream object");

        $res = new Response(200, "", "1.0", $oHeaders, $oBody);
        $this->assertTrue(is_a($res, Response::class));
    }


    public function test_method_get_status_code()
    {
        $res = prov_instanceOf_Http_Response(200);
        $this->assertSame(200, $res->getStatusCode());
    }


    public function test_method_get_reason_phrase()
    {
        $res = prov_instanceOf_Http_Response(200, "");
        $this->assertSame("OK", $res->getReasonPhrase());

        $res = prov_instanceOf_Http_Response(200, "Teste");
        $this->assertSame("Teste", $res->getReasonPhrase());
    }





    public function test_method_clone_with_status()
    {
        $res = prov_instanceOf_Http_Response();
        $this->assertSame(200, $res->getStatusCode());
        $this->assertSame("OK", $res->getReasonPhrase());

        $res1 = $res->withStatus(404, "Not Here ;( ");
        $this->assertSame(404, $res1->getStatusCode());
        $this->assertSame("Not Here ;( ", $res1->getReasonPhrase());
        $this->assertSame(200, $res->getStatusCode());
        $this->assertSame("OK", $res->getReasonPhrase());

        $res2 = $res->withStatus(500);
        $this->assertSame(500, $res2->getStatusCode());
        $this->assertSame("Internal Server Error", $res2->getReasonPhrase());
    }


    public function test_method_clone_with_viewdata()
    {
        $res = prov_instanceOf_Http_Response();
        $this->assertSame(null, $res->getViewData());

        $viewData = (object)["key1" => "val1", "key2" => "val2"];

        $res1 = $res->withViewData($viewData);
        $this->assertTrue(is_a($res1->getViewData(), "StdClass"));
        $this->assertSame("val1", $res1->getViewData()->key1);
        $this->assertSame("val2", $res1->getViewData()->key2);
    }


    public function test_method_clone_with_viewconfig()
    {
        $res = prov_instanceOf_Http_Response();
        $this->assertSame(null, $res->getViewConfig());

        $viewConfig = (object)["key1" => "val1", "key2" => "val2", "responseMime" => "text/plain"];

        $res1 = $res->withViewConfig($viewConfig);
        $this->assertTrue(is_a($res1->getViewConfig(), "StdClass"));
        $this->assertSame("val1", $res1->getViewConfig()->key1);
        $this->assertSame("val2", $res1->getViewConfig()->key2);
        $this->assertSame("text/plain", $res1->getViewConfig()->responseMime);
    }


    public function test_method_clone_with_headers()
    {
        $headers    = prov_assocArray_to_Http_Header_02();
        $oHeaders   = prov_instanceOf_Http_HeaderCollection_01($headers);
        $oBody      = prov_instanceOf_Http_Stream_fromString("Test stream object");

        $res = prov_instanceOf_Http_Response(200, "", "1.1", $oHeaders, $oBody);
        $this->assertTrue($res->hasHeader("CONTENT_TYPE"));
        $this->assertTrue($res->hasHeader("teste"));
        $this->assertTrue($res->hasHeader("unique"));
        $this->assertFalse($res->hasHeader("accept"));
        $this->assertFalse($res->hasHeader("accept-language"));


        // Subtitui integralmente os headers
        $headers    = prov_assocArray_to_Http_Header_03();
        $oHeaders02 = prov_instanceOf_Http_HeaderCollection_01($headers);
        $res1 = $res->withHeaders($oHeaders02->toArray());
        $this->assertTrue($res1->hasHeader("CONTENT_TYPE"));
        $this->assertTrue($res1->hasHeader("teste"));
        $this->assertFalse($res1->hasHeader("unique"));
        $this->assertTrue($res1->hasHeader("accept"));
        $this->assertTrue($res1->hasHeader("accept-language"));
    }


    public function test_method_clone_with_headers_merge()
    {
        $headers    = prov_assocArray_to_Http_Header_02();
        $oHeaders   = prov_instanceOf_Http_HeaderCollection_01($headers);
        $oBody      = prov_instanceOf_Http_Stream_fromString("Test stream object");

        $res = prov_instanceOf_Http_Response(200, "", "1.1", $oHeaders, $oBody);
        $this->assertTrue($res->hasHeader("CONTENT_TYPE"));
        $this->assertTrue($res->hasHeader("teste"));
        $this->assertTrue($res->hasHeader("unique"));
        $this->assertFalse($res->hasHeader("accept"));
        $this->assertFalse($res->hasHeader("accept-language"));


        // Efetua o merge entre os arrays existentes e os definidos
        $headers    = prov_assocArray_to_Http_Header_03();
        $oHeaders02 = prov_instanceOf_Http_HeaderCollection_01($headers);
        $res1 = $res->withHeaders($oHeaders02->toArray(), true);
        $this->assertTrue($res1->hasHeader("CONTENT_TYPE"));
        $this->assertTrue($res1->hasHeader("teste"));
        $this->assertTrue($res1->hasHeader("unique"));
        $this->assertTrue($res1->hasHeader("accept"));
        $this->assertTrue($res1->hasHeader("accept-language"));
    }


    public function test_method_clone_with_action_properties()
    {
        $headers    = prov_assocArray_to_Http_Header_02();
        $oHeaders   = prov_instanceOf_Http_HeaderCollection_01($headers);
        $oBody      = prov_instanceOf_Http_Stream_fromString("Test stream object");

        $res = prov_instanceOf_Http_Response(200, "", "1.1", $oHeaders, $oBody);
        $this->assertSame(null, $res->getViewData());
        $this->assertSame(null, $res->getViewConfig());

        $this->assertTrue($res->hasHeader("CONTENT_TYPE"));
        $this->assertTrue($res->hasHeader("teste"));
        $this->assertTrue($res->hasHeader("unique"));
        $this->assertFalse($res->hasHeader("accept"));
        $this->assertFalse($res->hasHeader("accept-language"));



        // Efetua o merge entre os arrays existentes e os definidos
        $headers    = prov_assocArray_to_Http_Header_03();
        $oHeaders02 = prov_instanceOf_Http_HeaderCollection_01($headers);
        $viewData = (object)["key1" => "val1", "key2" => "val2"];
        $viewConfig = (object)["key1" => "val1", "key2" => "val2", "responseMime" => "text/plain"];


        $res1 = $res->withActionProperties(
            $viewData,
            $viewConfig,
            $oHeaders02->toArray()
        );

        $this->assertTrue(is_a($res1->getViewData(), "StdClass"));
        $this->assertSame("val1", $res1->getViewData()->key1);
        $this->assertSame("val2", $res1->getViewData()->key2);

        $this->assertTrue(is_a($res1->getViewConfig(), "StdClass"));
        $this->assertSame("val1", $res1->getViewConfig()->key1);
        $this->assertSame("val2", $res1->getViewConfig()->key2);
        $this->assertSame("text/plain", $res1->getViewConfig()->responseMime);

        $this->assertTrue($res1->hasHeader("CONTENT_TYPE"));
        $this->assertTrue($res1->hasHeader("teste"));
        $this->assertTrue($res1->hasHeader("unique"));
        $this->assertTrue($res1->hasHeader("accept"));
        $this->assertTrue($res1->hasHeader("accept-language"));
    }
}

<?php

namespace MarcinOrlowski\ResponseBuilder\Tests;

use MarcinOrlowski\ResponseBuilder\ResponseBuilder;
use Symfony\Component\HttpFoundation\Response as HttpResponse;

/**
 * Laravel API Response Builder
 *
 * @package   MarcinOrlowski\ResponseBuilder
 *
 * @author    Marcin Orlowski <mail (#) marcinorlowski (.) com>
 * @copyright 2016-2017 Marcin Orlowski
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      https://github.com/MarcinOrlowski/laravel-api-response-builder
 */
class SuccessTest extends Base\ResponseBuilderTestCaseBase
{
	/**
	 * Check success()
	 *
	 * @return void
	 */
	public function testSuccess()
	{
		$this->response = ResponseBuilder::success();
		$j = $this->getResponseSuccessObject();

		$this->assertNull($j->data);
	}

	/**
	 * Tests success() with custom API code
	 *
	 * @return void
	 */
	public function testSuccess_ApiCode()
	{
		for ($i=0; $i<20; $i++) {
			$api_code = mt_rand($this->min_allowed_code, $this->max_allowed_code);

			$this->response = ResponseBuilder::success(null, $api_code);
			$j = $this->getResponseSuccessObject($api_code);

			$this->assertNull($j->data);
			$this->assertEquals($api_code, $j->code);
		}

	}

	/**
	 * Checks success() with valid payload and HTTP code
	 *
	 * @return void
	 */
	public function testSuccess_DataAndHttpCode()
	{
		$payloads = [
			null,
			[$this->getRandomString() => $this->getRandomString()],
		];
		$http_codes = [HttpResponse::HTTP_OK       => null,
		               HttpResponse::HTTP_ACCEPTED => HttpResponse::HTTP_ACCEPTED,
		               HttpResponse::HTTP_OK       => HttpResponse::HTTP_OK];

		/** @var \MarcinOrlowski\ResponseBuilder\ApiCodeBase $api_codes_class_name */
		$api_codes_class_name = $this->getApiCodesClassName();

		foreach ($payloads as $payload) {
			foreach ($http_codes as $http_code_expect => $http_code_send) {
				$this->response = ResponseBuilder::success($payload, null, [], $http_code_send);

				$j = $this->getResponseSuccessObject($api_codes_class_name::OK, $http_code_expect);

				if ($payload !== null) {
					$payload = (object)$payload;
				}
				$this->assertEquals($payload, $j->data);
			}
		}
	}

	/**
	 * @return void
	 *
	 * Tests successWithHttpCode()
	 */
	public function testSuccessHttpCode()
	{
		$http_codes = [HttpResponse::HTTP_ACCEPTED,
		               HttpResponse::HTTP_OK];
		foreach ($http_codes as $http_code) {
			$this->response = ResponseBuilder::successWithHttpCode($http_code);
			$j = $this->getResponseSuccessObject(0, $http_code);
			$this->assertNull($j->data);
		}
	}


	//----[ success ]-------------------------------------------

	/**
	 * @return void
	 *
	 * @expectedException \InvalidArgumentException
	 */
	public function testSuccessErrorCodeMustBeInt()
	{
		ResponseBuilder::success(null, 'foo');
	}

	/**
	 * @return void
	 *
	 * @expectedException \InvalidArgumentException
	 */
	public function testSuccess_HttpCodeNull()
	{
		$this->response = ResponseBuilder::successWithHttpCode(null);
	}

	/**
	 * @return void
	 *
	 * @expectedException \InvalidArgumentException
	 */
	public function testSuccessWithInvalidHttpCode()
	{
		ResponseBuilder::successWithHttpCode('invalid');
	}

	/**
	 * @return void
	 *
	 * @expectedException \InvalidArgumentException
	 */
	public function testSuccessWithTooBigHttpCode()
	{
		ResponseBuilder::successWithHttpCode(666);
	}

	/**
	 * @return void
	 *
	 * @expectedException \InvalidArgumentException
	 */
	public function testSuccessWithTooLowHttpCode()
	{
		ResponseBuilder::successWithHttpCode(0);
	}

	/**
	 * @return void
	 *
	 * @expectedException \InvalidArgumentException
	 */
	public function testBuildSuccessResponse_InvalidReturnCode()
	{
		$obj = new ResponseBuilder();
		$method = $this->getProtectedMethod(get_class($obj), 'buildSuccessResponse');
		$method->invokeArgs($obj, [null,
		                           'string-is-invalid-code']);
	}

}

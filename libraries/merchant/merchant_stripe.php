<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
 * CI-Merchant Library
 *
 * Copyright (c) 2012 Crescendo Multimedia Ltd
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:

 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.

 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

/**
 * Merchant Stripe Class
 *
 * Payment processing using Stripe (https://stripe.com/)
 */

class Merchant_stripe extends Merchant_driver
{
	const API_ENDPOINT = 'https://api.stripe.com';

	public function default_settings()
	{
		return array(
			'api_key' => '',
		);
	}

	public function purchase()
	{
		$this->require_params('token', 'reference');

		$request = array(
			'amount' => round($this->param('amount') * 100),
			'card' => $this->param('token'),
			'currency' => strtolower($this->param('currency')),
			'description' => $this->param('reference'),
		);

		$response = Merchant::curl_helper(self::API_ENDPOINT.'/v1/charges', $request, $this->setting('api_key'));
		if ( ! empty($response['error'])) return new Merchant_response(Merchant_response::FAILED, $response['error']);

		$data = json_decode($response['data']);
		if (isset($data->error))
		{
			return new Merchant_response(Merchant_response::FAILED, $data->error->message);
		}
		else
		{
			return new Merchant_response(Merchant_response::COMPLETED, '', $data->id, $data->amount / 100);
		}
	}
}

/* End of file ./libraries/merchant/drivers/merchant_stripe.php */
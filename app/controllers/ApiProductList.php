<?php

class ApiProductList extends BaseController {

	/**
	 * Display a product listing of the resource.
	 *
	 * @return Response
	 */
	public function index() {

		try {
			$products = ProductList::all();

			/*$items = $products->toArray();
			$totalItems = ProductLists::count();
			$perPage = 10;

			$product = Paginator::make($items, $totalItems, $perPage);*/
			// DebugHelper::log(__METHOD__, $products->toArray());
			return Response::json(array(
				'error' => false,
				'message' => 'Success',
				'result' => $products->toArray()),
				200
			);

		}catch(Exception $e) {
			return Response::json(array(
				"error" => true,
				"result" => $e->getMessage()),
				400
			);
		}
	}

	public function checkUpc() {
		try {
			CommonHelper::setRequiredFields(array('upc'));

			$upc     = Request::get('upc');

			ProductList::checkIfUpcExist($upc);

			return CommonHelper::return_success();

		}catch(Exception $e) {
			Log::error(__METHOD__ .' Something went wrong: '.print_r($e->getMessage(),true));
			return CommonHelper::return_fail($e->getMessage());
		}
	}
}
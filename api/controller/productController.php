<?php

namespace Api\controller;

require_once 'action_route/Product.php';
require_once 'inc/Response.php';
require_once 'inc/Filter.php';
require_once 'controller.php';

use Api\action_route\Product;
use Api\inc\Response;
use Api\inc\Filter;
use Api\controller\Controller;

use Exception;


class ProductController extends Controller{
    use Response;
    use Filter;

    public function __construct(private array $produt_parameters) {
    }

    public function get_products()
    {

        $filters = $this->payment_parameters['filter'] ?? '';
        $paramenters = $this->getFilter($filters);

        //set products parameters
        $product = new Product();
        self::setClassParameters($product, $paramenters);

        //get and return products
        return $product->get_products();
    }

    public function create_product()
    {
        //set product parameters
        $product = new Product();
        self::setClassParameters($product, $this->produt_parameters);

        //avoid duplicate product
        $ProductExist = $product->checkProductExists();

        if ($ProductExist) {
            return $this->responseError('the product is already registered');
        };

        //create Product
        return $product->create_product();
    }

    public function update_product()
    {
        //set product parameters
        $product = new Product();
        self::setClassParameters($product, $this->produt_parameters);

        //avoid duplicate product
        $ProductExist = $product->checkProductExists();

        if ($ProductExist) {
            return $this->responseError('the product is already registered');
        };

        //update product
        return $product->update_product();
    }

    public function destroy_product()
    {
        //set product parameters
        $product = new Product();
        self::setClassParameters($product, $this->produt_parameters);

        //avoid removing already removed product
        $productExist = $product->checkProductExists();

        if (!$productExist) {
            return $this->responseError('the product not found, try again later!');
        }

        //destroy product
        return $product->destroy_product();
    }

}
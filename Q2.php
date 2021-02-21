<?php
/**
 * 問題二：
 * 請你使用 PHP 在不使用 DB 的狀況下實作出購物車功能，且須滿足以下功能。
 * 1. 新增商品
 * 2. 移除商品
 * 3. 更新商品數量
 * 4. 取得購物車總共價格
 * 5. 取得購物車內項目清單列表(顯示品名、數量、單價、總價格)
 *
 */

/**
 * 定義商品
 */
class Product{
    public $name;
    public $price;

    /**
     * Product
     * @param $name
     * @param $price
     */
    public function __construct($name, $price)
    {
        $this->name = $name;
        $this->price = $price;
    }
}

/**
 * 建立商品
 * @param String $name
 * @param int $price
 * @return Product
 */
function NewProduct(String $name, int $price):Product{
    return new Product($name, $price);
}

/**
 * 1. 新增商品
 * @param Product $product
 * @param int $amount
 */
function AddtoCart(Product $product, int $amount) {
    if(!! in_array($product->name,array_keys($GLOBALS['shoppingCart']))) {
        $GLOBALS['shoppingCart'][$product->name]['amount'] += $amount;
    }
    else {
        $GLOBALS['shoppingCart'][$product->name]['amount'] = $amount;
        $GLOBALS['shoppingCart'][$product->name]['price'] = $product->price;
    }
}

/**
 * 2. 移除商品
 * @param Product $product
 */
function RemoveProduct(Product $product){
    if(!! in_array($product->name,array_keys($GLOBALS['shoppingCart']))) {
        unset($GLOBALS['shoppingCart'][$product->name]);
    }
}

/**
 * 3. 更新商品數量
 * @param Product $product
 * @param int $amount
 */
function UpdateProduct(Product $product, int $amount){
    $GLOBALS['shoppingCart'][$product->name]['amount'] = $amount;
}

/**
 * 4. 取得購物車總共價格
 */
function TotalPrice() {
    $sum = 0;
    foreach ($GLOBALS['shoppingCart'] as $product){
        $sum += $product['amount'] * $product['price'];
    }
    return $sum;
}

/**
 * 5. 取得購物車內項目清單列表(顯示品名、數量、單價、總價格)
 */
function CartDetail() {
    print_r($GLOBALS['shoppingCart']);
    print('總價格: '.TotalPrice());
}

/**
 * 模擬商品 & 購物車
 */
$milk = NewProduct('Milk',80);
$cola = NewProduct('Cola',29);
$tea = NewProduct('Tea',20);
$shoppingCart = [];

/**
 * 模擬操作
 */
AddtoCart($milk,10);
AddtoCart($cola,10);
AddtoCart($milk,10);
RemoveProduct($milk);
UpdateProduct($cola,1);
CartDetail();


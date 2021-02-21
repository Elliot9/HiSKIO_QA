<?php
/**
 * 問題三：
 * 小明任職於一家電商的新創公司，銷售的商品會上架於 Pchome, Yahoo, Ruten, Shopee 等拍賣平台，小明的任務是當一個產品發佈(publish)的時候，能夠馬上通知所有平台產品已經發佈，當平台收到通知後就會回傳「Pchome 已收到商品發佈通知」字樣，Yahoo, Ruten, Shopee 以此類推，並且可以隨時增加、減少任一個通路的通知，比如拿掉 Yahoo，新增 Shopee 的通路，請您設計出一個可以隨時增加、減少需求的程式碼架構，幫助小明達到此功能！
 *
 * 執行檔案部分內容：
 * ...
 * ...
 * // attach shopee
 * // detach yahoo
 * $product->publish();
 *
 * 執行結果：
 * Pchome 已收到商品發佈通知
 * Ruten 已收到商品發佈通知
 * Shopee 已收到商品發佈通知
 */


interface platformInterface {
    /**
     * @param Product $product
     * @return mixed
     */
    public function publishProductNotification(Product $product);
}
/**
 * 定義 模擬平台
 */
abstract class platform implements platformInterface {
    private $platformName;
    private $platformApi;
    public function __construct() {
        $this->platformName = $this->setPlatformName();
        $this->platformApi = $this->setPlatformApi();
    }

    protected abstract function setPlatformName();
    protected abstract function setPlatformApi();

    /**
     * 模擬通知平台
     * 當平台回傳觸發 Event
     *
     * @param Product $product
     * @return void
     * @throws Exception
     */
    public function publishProductNotification(Product $product) {
        try {
            $ch = curl_init($this->platformApi);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(['product' => $product->name]));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                    'Content-Type: application/json',
                    'Content-Length: ' . strlen(json_encode(['product' => $product->name])))
            );
            $response = curl_exec($ch);
            $responseCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            if (!($response === false || $responseCode >= 400)) {
                Event::trigger($product, $this->platformName);
            }
            curl_close($ch);
        }
        catch (Exception $exception) {
            echo ('Error: '.$exception->getMessage());
        }
    }
}
/**
 * 模擬平台 Pchome
 * 傳入平台名稱 & 通知接口
 */
class Pchome extends platform {
    protected function setPlatformName() {
       return 'Pchome';
    }
    protected function setPlatformApi() {
        return '127.0.0.1/Pchome';
    }
}
/**
 * 模擬平台 Yahoo
 * 傳入平台名稱 & 通知接口
 */
class Yahoo extends platform {
    protected function setPlatformName() {
        return 'Yahoo';
    }
    protected function setPlatformApi() {
        return '127.0.0.1/Yahoo';
    }
}
/**
 * 模擬平台 Ruten
 * 傳入平台名稱 & 通知接口
 */
class Ruten extends platform {
    protected function setPlatformName() {
        return 'Ruten';
    }
    protected function setPlatformApi() {
        return '127.0.0.1/Ruten';
    }
}
/**
 * 模擬平台 Shopee
 * 傳入平台名稱 & 通知接口
 */
class Shopee extends platform {
    protected function setPlatformName() {
        return 'Shopee';
    }
    protected function setPlatformApi() {
        return '127.0.0.1/Shopee';
    }
}


/**
 * 定義事件
 */
class Event {
    private static $events = [];

    /**
     * 事件監聽
     * @param Product $product
     * @param $platform
     * @param callable $callback
     */
    public static function listen(Product $product, $platform, callable $callback) {
        self::$events[$product->name][$platform] = $callback;
    }

    /**
     * 事件觸發
     * @param Product $product
     * @param $platform
     */
    public static function trigger(Product $product, $platform) {
        if(array_key_exists($platform, self::$events[$product->name] ?? [])) {
            call_user_func(self::$events[$product->name][$platform], $platform);
        }
    }

    /**
     * 重置事件監聽
     * @param Product $product
     */
    public static function initListen(Product $product) {
        unset(self::$events[$product->name]);
    }
}


/**
 * 定義產品
 */
class Product{
    public $name;
    public $platforms;
    public function __construct(String $name, array $platforms = []) {
        $this->name = $name;
        $this->platforms = $platforms;
    }

    /**
     * 加入通知平台
     * @param $platform
     */
    public function attach($platform) {
        in_array($platform,$this->platforms) ?: $this->platforms[] = $platform;
    }

    /**
     * 移除通知平台
     * @param $platform
     */
    public function detach($platform) {
        $key = array_search($platform, $this->platforms);
        if($key) {
            unset($this->platforms[$key]);
        }
    }

    /**
     *　綁定事件監聽
     */
    public function bindListen() {
        Event::initListen($this);
        foreach ($this->platforms as $platform) {
            Event::listen($this, $platform, function ($platform) {
                echo $platform." 已收到 ".$this->name." 發佈通知\n";
            });
        }
    }

    /**
     *　發布消息
     * 通知各平台
     */
    public function sentNotification() {
        foreach ($this->platforms as $platform) {
            if(!isset($$platform)) {
                $$platform = new $platform;
            }
            $$platform->publishProductNotification($this);
        }
    }

    /**
     * 產品發佈
     * 綁定事件監聽後發布消息
     */
    public function publish() {
        $this->bindListen();
        $this->sentNotification();
    }
}



/**
 * 模擬產品發布
 */
$apple = new Product('Apple');
$apple->attach('Shopee');
$apple->attach('Yahoo');
$apple->attach('Pchome');
$apple->detach('Pchome');
$apple->publish();

$banana = new Product('Banana');
$banana->attach('Shopee');
$banana->publish();

$car = new Product('Car', ['Shopee','Yahoo','Ruten']);
$car->publish();




<?php
namespace FireKit\Utils;
/**
 * User: olton
 * Date: 06.08.11
 * Time: 19:40
 */

define('CART_MODE_SESSION', 1);
define('CART_MODE_COOKIES', 2);

  /**
   * Cart item class
  */
class CartItem {
    var $_itemID;
    var $_itemData;

    /**
     * Constructor of Cart Item Class
     * @param mixed $itemID Item ID
     * @param mixed $itemData Item data
     */
    function __construct($itemID, $itemData){
        $this->_itemID = $itemID;
        $this->_itemData = $itemData;
    }
}

  /**
   * Cart class
  */
class Cart extends \FireKit\Base\Super {
    private static $_instance; //Instance of cart
    private $_cart = array(); // Cart Data
    private $_flushMode; //Cart store mode (COOKIES or SESSION)
    private $_cookie_lifetime;

    public function SetCookieLifeTime($time = 86400){
        $this->_cookie_lifetime = time() + $time;
    }

    /**
     * Constructor of Cart Class
     * @param int $mode (1 or CART_MODE_SESSION || 2 or CART_MODE_COOKIES) Cart store mode
    */
    private function __construct($mode){
        $this->SetFlushMode($mode);
        if ($this->_flushMode == CART_MODE_SESSION) {
            if (!isset($_SESSION['cart'])) {$this->FlushCart();}
            $this->_cart = unserialize( $_SESSION['cart'] );
        } else {
            if (isset($_COOKIE)) {
                $this->_cart = unserialize( $_COOKIE['cart'] );
            } else {
                $this->SetFlushMode(CART_MODE_SESSION);
                $this->_cart = unserialize( $_SESSION['cart'] );
            }
        }
    }

    /**
     * Desstructor of Cart Class
    */
    function __destruct(){
        $this->FlushCart();
    }

    /**
     * function for safe create of Cart Instance
     * @param int $mode (1 or CART_MODE_SESSION || 2 or CART_MODE_COOKIES) Cart store mode
     * Use: $cart = Cart::CreateInstance();
    */
    public static function CreateInstance($mode = CART_MODE_SESSION){
        if (self::$_instance == null || !self::$_instance instanceof Cart) {
            self::$_instance = new Cart($mode);
        }
        return self::$_instance;
    }

    /**
     * function for set Cart store mode
     * @param int $mode (1 or CART_MODE_SESSION || 2 or CART_MODE_COOKIES) Cart store mode
    */
    public function SetFlushMode($mode = CART_MODE_SESSION){
        $this->_flushMode = $mode;
    }

    /**
     * function for get Cart store mode
    */
    public function GetFlushMode(){
        return $this->_flushMode;
    }

    /**
     * function for Add Item to Cart
     * @param mixed $itemID
     * @param mixed $itemData
     * @param int $count
    */
    public function AddItems($itemID, $itemData = null, $count = 1){
        if ((int)$count <= 0) return;
        if (isset($this->_cart[$itemID])) {
            $this->_cart[$itemID]['count'] += $count;
        } else {
            $this->_cart[$itemID]['count'] = $count;
            $this->_cart[$itemID]['data'] = new CartItem($itemID, $itemData);
        }
        $this->FlushCart();
    }

    /**
     * function for Del Item from Cart
     * @param mixed $itemID
     * @param int $count
    */
    public function RemoveItems($itemID, $count){
        if ($count < 0) return;
        if (isset($this->_cart[$itemID])) {
            if ($this->_cart[$itemID]['count'] > $count) {
                $this->_cart[$itemID]['count'] -= $count;
            } else {
                unset($this->_cart[$itemID]);
            }
        }
        $this->FlushCart();
    }

    public function DeletePosition($itemID){
        if (isset($this->_cart[$itemID])) {
            unset($this->_cart[$itemID]);
        }
        $this->FlushCart();
    }

    /**
     * function for empties Cart
    */
    public function EmptyCart(){
        unset($this->_cart);
        $this->FlushCart();
    }

    /**
     * function return Cart
    */
    public function GetCart(){
        return $this->_cart;
    }

    /**
     * function return Items total count from Cart
    */
    public function GetItemsCount(){
        $count_total = 0;
        if ($this->_cart == null && count($this->_cart)==0) return 0;
        foreach($this->_cart as $cart_position){
            $count_total += $cart_position['count'];
        }
        return (int)$count_total;
    }

    /**
     * function return Count Positions from Cart
    */
    public function GetPositionsCount(){
        return (int)count($this->_cart);
    }

    /**
     * function for store Cart into SESSION or COOKIES
    */
    public function FlushCart(){
        if ($this->_flushMode === CART_MODE_SESSION) {
            $_SESSION['cart'] = serialize( $this->GetCart() );
        } else {
            setcookie("cart", serialize($this->_cart), $this->_cookie_lifetime);
        }
    }
}
?>

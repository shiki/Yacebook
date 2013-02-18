<?php

namespace Yacebook;

/**
 * A Yii application component that provides easy access to the
 * {@link https://github.com/facebook/facebook-php-sdk Facebook PHP SDK}. This allows Facebook
 * API configurations to be set up in the Yii config files and then easily create Facebook clients
 * using those configurations anywhere.
 *
 * To use, add this as a Yii component, and set the `connections` property to an array of
 * different Facebook clients that you want to use. Most of the time, you only need one. Different
 * clients are set with different array keys.
 *
 * The Facebook SDK files are not included in this library. You need to download it yourself and
 * then set the `sdkLibPath` to its location.
 *
 * Here's an example Yii configuration using this library:
 *
 * <code>
 * ...
 * 'components' => array(
 *   'yacebook' => array(
 *     'class' => '\\Yacebook\\Yacebook',
 *     'sdkLibPath' => '/path/to/facebook/src/folder',
 *     'connections' => array(
 *       'default' => array(
 *         'options' => array(
 *           'appId' => 'YOUR-APP-ID',
 *           'secret' => 'YOUR-APP-SECRET',
 *         ),
 *       ),
 *     ),
 *   ),
 * )
 * ...
 * </code>
 *
 * Then, access your client by:
 *
 * <code>
 * $client = \Yii::app()->yacebook->getClient('default');
 * </code>
 *
 * Or, just:
 *
 * <code>
 * $client = \Yii::app()->yacebook->getClient(); // Assumed "default"
 * </code>
 *
 * The above will return an instance of {@link Facebook} created using the value of `options`
 * in the connection configuration used as the parameter in the constructor. If a client with
 * the same configuration was previously created, {@link getClient} will return the previously
 * created client instance. If you do not want this behavior, you can use {@link createClient}.
 *
 * @author Shiki <shikishiji@gmail.com>
 */
class Yacebook extends \CApplicationComponent
{
  /**
   * Path to the src folder of the Facebook PHP SDK (https://github.com/facebook/facebook-php-sdk).
   * If you want to load the SDK classes yourself, you don't need to specify this.
   *
   * @var string
   */
  public $sdkLibPath;

  protected $_clients = array();

  /**
   * Client configurations. This is normally set up in Yii config files. This is an array
   * containing configurations for {@link Facebook} instances that will be created using this
   * application component.
   *
   * Currently, the array items can only contain an `options` property. The value of this property
   * will be used as the parameter for the {@link Facebook} constructor.
   *
   * Sample value:
   *
   * <code>
   * array(
   *   'default' => array(
   *     'options' => array(
   *       'appId' => 'YOUR-APP-ID',
   *       'secret' => 'YOUR-APP-SECRET',
   *     ),
   *   ),
   * )
   * </code>
   *
   * @var array
   */
  public $connections = array(
    'default' => array(
      // The value passed on the Facebook class constructor
      'options' => array(
        'appId' => '',
        'secret' => '',
      ),
    ),
  );

  /**
   * {@inheriteddoc}
   */
  public function init()
  {
    parent::init();

    if (class_exists('Facebook', false))
      return;

    // Load Facebook class
    require($this->sdkLibPath . '/facebook.php');
  }

  /**
   * Get an instance of \Facebook using the configuration pointed to by `$connectionKey`. This will
   * store the created instance locally and subsequent calls to this method using the same `$connectionKey`
   * will return the already created client.
   *
   * @param string $connectionKey The connection configuration key that can be found in {@link $connections}.
   *
   * @return \Facebook
   */
  public function getClient($connectionKey = 'default')
  {
    if (isset($this->_clients[$connectionKey]))
      return $this->_clients[$connectionKey];

    $client = $this->createClient($connectionKey);
    $this->_clients[$connectionKey] = $client;

    return $client;
  }

  /**
   * Create an instance of \Facebook using the configuration pointed to by `$connectionKey`.
   *
   * @param string $connectionKey The connection configuration key that can be found in {@link $connections}.
   *
   * @return \Facebook
   */
  public function createClient($connectionKey = 'default')
  {
    $client = new \Facebook($this->connections[$connectionKey]['options']);

    return $client;
  }
}



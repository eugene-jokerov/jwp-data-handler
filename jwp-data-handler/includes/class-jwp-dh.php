<?php
 
defined( 'ABSPATH' ) || exit;

/**
 * JWP_DH - основной класс системы
 * 
 * Основной класс системы JWP Data Handler. Реализует паттерн Singleton для удобного вызова из кода темы или других плагинов.
 */
final class JWP_DH {
	
	/**
     * Абсолютный путь к папке плагина
     *
     * @var string
     */
	protected $plugin_dir;
	
	/**
     * Единственный экземпляр класса
     *
     * @var JWP_DH
     */
	private static $_instance = null;
	
	/**
     * Массив для хранения пользовательских обработчиков
     *
     * @var array
     */
	protected $handlers = array();
	
	/**
     * Ограничивает клонирование объекта
     *
     * @return void
     */
	protected function __clone() {
		
	}
	
	/**
     * Ограничивает создание другого экземпляра класса через сериализацию
     *
     * @return void
     */
	protected function __wakeup() {
		
	}
	
	/**
     * Возвращает единственный экземпляр класса.
     *
     * @return JWP_DH
     */
	static public function instance() {
		if( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}
	
	/**
     * Инициализация плагина. Создание страницы плагина в админке и добавление обработчика ajax
     *
     * @return void
     */
	private function __construct() {
		$this->plugin_dir = JWP_DH_PLUGIN_DIR;
		add_action( 'admin_menu', array( $this, 'register_menu' ) );
		add_action( 'wp_ajax_jwp_dh_callback', array( $this, 'ajax_callback' ) );
	}
	
	/**
     * Возвращает обработчик по его уникальному идентификатору
     *
     * @param string $handler_slug
     * @return JWP_Data_Handler
     */
	protected function get_handler( $handler_slug ) {
		if ( isset( $this->handlers[ $handler_slug ] ) ) {
			return $this->handlers[ $handler_slug ];
		}
		return false;
	}
	
	/**
     * Проверяет объект $handler на соответствие спецификации
     *
     * @param JWP_Data_Handler $handler
     * @return bool
     */
	protected function is_valid_handler( $handler ) {
		return true; // реализовать функционал проверки handler
	}
	
	/**
     * Создаёт основную страницу плагина в админке и ссылку в меню
     *
     * @return void
     */
	public function register_menu() {
		add_menu_page( 'JWP Data Handler', 'JWP Data Handler', 'manage_options', JWP_DH_MENU_SLUG, array( $this, 'admin_page' ), '', 99 );
	}
	
	/**
     * Метод отвечает за вывод содержимого основной страницы плагина
     *
     * @return void
     */
	public function admin_page() {
		include_once( $this->plugin_dir . 'views/admin_page.php' );
	}
	
	/**
     * Обработчик ajax запросов
     *
     * @return void
     */
	public function ajax_callback() {
		check_ajax_referer( 'jwp-dh' ); // проверка nonce
		if( ! current_user_can( 'manage_options' ) ) { 
			wp_die(); // у текущего пользователя недостаточно прав
		}
		
		// получаем параметры запроса
		$handler_slug = $_POST['slug']; // уникальный идентификатор обработчика
		$offset       = intval( $_POST['offset'] ); // отступ от первого элемента
		$total        = $_POST['total']; // всего элементов
		
		$response = new JWP_DH_Response;
		$handler = $this->get_handler( $handler_slug ); 
		if ( ! $handler ) {
			wp_die(); // запрашиваемый обработчик не найден
		}
		
		// вычисляем общее кол-во элементов в выборке
		if ( $total == '?' ) {
			$total = $handler->total();
		}
		$total = intval( $total );
		
		// устанавливаем параметры ответа до обработки
		$response->set( 'offset', $offset );
		$response->set( 'total', $total );
		
		$response = $handler->process( $response ); // передаём управление в пользовательский обработчик
		
		// просчитываем и устанавливаем параметры после обработки
		$offset = $response->get( 'offset' );
		$total  = $response->get( 'total' );
		$max_process_elements = $handler->max_process_elements; 
		$offset = $offset + $max_process_elements;
		$response->set( 'offset', $offset );
		if ( $offset >= $total ) {
			$response->set( 'offset', $total );
		}
		
		// возвращаем данные в json формате
		wp_send_json( $response->prepare_to_send() );
	}
	
	/**
     * Добавляет пользовательский обработчик в систему
     *
     * @param JWP_Data_Handler $handler
     * @return bool
     */
	public function add_handler( $handler ) {
		// проверки объекта $handler
		if ( ! $this->is_valid_handler( $handler ) ) {
			return false;
		}
		
		if ( ! isset ( $this->handlers[ $handler->slug ] ) ) {
			$this->handlers[ $handler->slug ] = $handler;
		} else {
			return false; // такой handler уже зареган
		}
		
		// php >= 5.3
		// добавляем страницу пользовательского обработчика и ссылку в субменю
		add_action( 'admin_menu', function() use ( $handler ) {
			add_submenu_page( JWP_DH_MENU_SLUG, $handler->name, $handler->name, 'manage_options', $handler->slug, array( $handler, 'admin_page' ) ); 
		} );
		
		// подключаем js скрипт на странице пользовательского обработчика
		add_action( 'admin_enqueue_scripts', function( $hook ) use ( $handler ) {
			// проверять $hook чтобы грузить скрипт только на нужных страницах
			wp_enqueue_script( 'jwp_dh_js', $handler->admin_page_js );
		} );
		return true;
	}
}

<?php

defined( 'ABSPATH' ) || exit;

/**
 * JWP_Data_Handler - базовый класс пользовательских обработчиков
 * 
 * Базовый класс для пользовательских обработчиков. Необходима реализация абстрактных методов
 */
if ( ! class_exists( 'JWP_Data_Handler' ) ) {
	abstract class JWP_Data_Handler {
		
		/**
		 * Имя обработчика
		 * 
		 * Используется для генерации анкора ссылки в субменю
		 *
		 * @var string
		 */
		public $name = '';
		
		/**
		 * Уникальный идентификатор обработчика
		 * 
		 * Используется для получения нужного обработчика и для генерации ссылки в субменю
		 *
		 * @var string
		 */
		public $slug = '';
		
		/**
		 * Заголовок на странице обработчика
		 *
		 * @var string
		 */
		public $title = 'JWP Data Handler';
		
		/**
		 * Максимальное кол-во элементов, обрабатываемое за 1 запрос
		 * 
		 * Это свойство можно использовать в пользовательских обработчиках для создания выборки данных
		 *
		 * @var int
		 */
		public $max_process_elements = 10;
		
		/**
		 * Путь к файлу, отвечающему за вывод страницы обработчика
		 *
		 * @var string
		 */
		public $admin_page_template = JWP_DH_PLUGIN_DIR . 'views/default_template.php';
		
		/**
		 * Ссылка на js файл обработчика
		 *
		 * @var string
		 */
		public $admin_page_js = JWP_DH_PLUGIN_URL . 'assets/js/data-handler.js';
		
		/**
		 * В этом методе необходимо реализовать механизм обработки данных
		 *
		 * @param JWP_DH_Response $response
		 * @return JWP_DH_Response
		 */
		abstract function process( $response );
		
		/**
		 * Вычисляет и возвращает максимальное кол-во элементов в выборке. Необходима реализация.
		 *
		 * @return int
		 */
		abstract function total();
		
		/**
		 * Подключает файл, отвечающий за содержимое страницы обработчика
		 *
		 * @return void
		 */
		public function admin_page() {
			if ( is_file( $this->admin_page_template ) ) {
				include_once( $this->admin_page_template );
			}
		}
		
		/**
		 * Вычисляет и возвращает максимальное кол-во элементов, обрабатываемое за 1 запрос
		 *
		 * @param string $total
		 * @param string $offset
		 * @return int
		 */
		public function get_max_process_elements( $total = 0, $offset = 0 ) {
			if ( ! $total ) {
				return $this->max_process_elements;
			}
			if ( ( $total - $offset ) < $this->max_process_elements ) {
				return $total - $offset;
			}
			return $this->max_process_elements;
		}
	}
}

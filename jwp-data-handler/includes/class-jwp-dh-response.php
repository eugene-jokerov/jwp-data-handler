<?php

defined( 'ABSPATH' ) || exit;

/**
 * JWP_DH_Response
 * 
 * Хранит состояние системы и используется для передачи его между backend и frontend
 */
if ( ! class_exists( 'JWP_DH_Response' ) ) {
	class JWP_DH_Response {
		/**
		 * Отступ от первого элемента выборки
		 *
		 * @var int
		 */
		protected $offset = 0;
		
		/**
		 * Всего элементов в выборке
		 *
		 * @var int
		 */
		protected $total = 0;
		
		/**
		 * Массив сообщений, которые надо передать на frontend
		 *
		 * @var array
		 */
		protected $output = array();
		
		/**
		 * Дополнительные пользовательские данные
		 *
		 * @var array
		 */
		protected $data = array();
		
		/**
		 * Возвращает указанный параметр
		 *
		 * @param string $property_name
		 * @return mixed
		 */
		public function get( $property_name ) {
			if ( ! $property_name ) return false;
			if ( isset( $this->$property_name ) ) {
				return $this->$property_name;
			}
			return false;
		}
		
		/**
		 * Устанавливает значение параметра
		 *
		 * @param string $property_name
		 * @param mixed $value
		 * @return void
		 */
		public function set( $property_name, $value ) {
			if ( isset( $this->$property_name ) ) {
				$this->$property_name = $value;
			}
		}
		
		/**
		 * Добавляет новое сообщение в массив сообщений
		 *
		 * @param string $value
		 * @return void
		 */
		public function output( $value ) {
			$this->output[] = $value;
		}
		
		/**
		 * Подготавливает параметры для отправки на frontend
		 *
		 * @return void
		 */
		public function prepare_to_send() {
			return array(
				'offset' => $this->get( 'offset' ),
				'total'  => $this->get( 'total' ),
				'output' => $this->get( 'output' ),
				'data'   => $this->get( 'data' ),
			);
		}

	}
}

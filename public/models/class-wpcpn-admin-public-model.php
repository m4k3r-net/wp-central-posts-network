<?php
/**
 *
 * @package WPCPN_Public
 * @author  Nícholas André <nicholas@iotecnologia.com.br>
 */

class WPCPN_Admin_Public_Model {

	const TABLE_SUFFIX		= 'wpcpn_featured_requests';
	const REQUEST_DUPLICATE	= 1;
	const REQUEST_OK		= 2;
	const REQUEST_ERROR		= 3;


	/**
	 * Carrega todas as informações referentes ao Model
	 *
	 * @since     1.0.0
	 */
	public function __construct() {
		add_action('wp_ajax_wpcpn_send_featured_request', 'WPCPN_Admin_Public_Model::send_featured_request');
	}

	/**
	 * Função callback para chamada ajax wpcpn_send_featured_request
	 * @see    public/assets/js/admin-public.js
	 * @param  $_GET['post_id'] ID do post solicitado
	 * @param  $_GET['blog_id'] ID do blog solicitante
	 * @return none
	 */
	public static function send_featured_request() {
		$post_id = (int) $_GET['post_id'];
		$blog_id = (int) $_GET['blog_id'];
		$message = esc_sql( $_GET['message'] );

		global $wpdb;


		echo self::insert_request( $blog_id, $post_id, $message );

		die();
	}

	public static function insert_request( $blog_id, $post_id, $message ) {
		global $wpdb;

		$count = $wpdb->get_var(
		 	$wpdb->prepare('SELECT COUNT(ID) FROM ' . self::get_table_name() . ' WHERE blog_id = %d AND post_id = %d' ,
		 		array(
		 			$blog_id,
		 			$post_id
		 		)
		 	)
		);

		if ( $count > 0 ) {
			return self::REQUEST_DUPLICATE;
		} else {
			$wpdb->insert( self::get_table_name(),
				array(
					'blog_id' => $blog_id,
					'post_id' => $post_id,
					'message' => $message
				),
				array(
					'%d',
					'%d',
					'%s'
				)
			);
			return self::REQUEST_OK;
		}

	}

	/**
	 * Atualiza o status de uma solicitação pendente para aprovado
	 * @param  int $blog_id ID do blog
	 * @param  int $post_id ID do post solicitante
	 */
	public static function approve( $blog_id, $post_id ) {
		return self::change_status( 'AP', $blog_id, $post_id );
	}

	/**
	 * Marca o status da solicitação como publicado
	 * @param  int $blog_id ID do blog
	 * @param  int $post_id ID do post solicitante
	 */
	public static function publish( $blog_id, $post_id ) {
		return self::change_status( 'PB', $blog_id, $post_id );
	}

	/**
	 * Altera o status de uma solicitação
	 * @param  String $status  Para qual status alterar
	 * @param  int $blog_id    ID do Blog
	 * @param  int $post_id    ID do Post
	 */
	public static function change_status( $status, $blog_id , $post_id ) {

		if ( ! in_array( $status, array('AP', 'AW', 'RJ', 'PB') ) )
			return false;


		$values = array(
				'status' => $status
		);

		$placeholders = array(
				'%s'
		);

		if ( $status == 'PB') {
			$values['published'] = date("Y-m-d H:i:s");
			$placeholders[] = '%s';
		}

		global $wpdb;
		$wpdb->update( self::get_table_name(),
			$values,
			array(
				'post_id' => $post_id,
				'blog_id' => $blog_id
			),
			$placeholders,
			array(
				'%d',
				'%d'
			)
		);

		return true;
	}

	public function get_request( $blog_id, $post_id ) {

		global $wpdb;
		$row = $wpdb->get_row(
		 	$wpdb->prepare('SELECT * FROM ' . self::get_table_name()  . ' WHERE blog_id = %d AND post_id = %d',
		 		array(
		 			$blog_id,
		 			$post_id
		 		)
		 	)
		);

		return $row;
	}

	/**
	 * Retorna o nome da tabela que armazena as solicitações
	 * @return string Table Name
	 */
	public static function get_table_name() {
		global $wpdb;

		return $wpdb->base_prefix . self::TABLE_SUFFIX;
	}


}

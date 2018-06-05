<?php
class RFSdbSession {

	public $db;
	private $table; 

	public function __construct() {
		$this->db = $GLOBALS['wpdb'];
		$this->table = 'session_storage';

		add_action( 'init', array($this, 'set_session_handler'), 9 );
		add_action( 'init', array($this, 'create_table'), 8 );
	}

	public function create_table() {
		$charset_collate 	= $this->db->get_charset_collate();

		$sql = "CREATE TABLE IF NOT EXISTS {$this->db->prefix}$this->table (
		id varchar(100) NOT NULL,
		data varchar(500) NOT NULL,
		timestamp varchar(255) NOT NULL,
		PRIMARY KEY  (id)
		) $charset_collate;";

		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		dbDelta( array($sql) );
	}

	public function set_session_handler() {
		session_set_save_handler(
			array($this, "_open"),
			array($this, "_close"),
			array($this, "_read"),
			array($this, "_write"),
			array($this, "_destroy"),
			array($this, "_gc")
		);
		if( !session_id() ) {
			session_start();
		}
	}

	public function _open() {
		if($this->db){
			return true;
		}
		return false;
	}

	public function _close(){
		if($this->db->close()) {
			return true;
		}
		return false;
	}

	public function _read($id) {
		$read = $this->db->get_row( $this->db->prepare( "SELECT data FROM {$this->db->prefix}".$this->table." WHERE id = '%s'", $id ) );
		if($read != null) {
			return $read->data;
		}
		return false;
	}

	public function _write($id, $data){
		$write = $this->db->query( $this->db->prepare( "REPLACE INTO {$this->db->prefix}".$this->table." VALUES ( %s, %s, %d );", $id, $data, time() ) );
		if( $write > 0 ) {
			return true;
		}
		return false;
	}

	public function _destroy($id){
		$delete = $this->db->query( $this->db->prepare( "DELETE FROM {$this->db->prefix}".$this->table." WHERE id = %s", $id ) );
		if( $delete > 0 ) {
			return true;
		}
		return false;
	}

	public function _gc($max){
		$gc = $this->db->query( $this->db->prepare( "DELETE FROM {$this->db->prefix}".$this->table." WHERE timestamp < %d", time() - $max ) );
		if( $gc > 0 ) {
			return true;
		}
		return false;
	}

}

new RFSdbSession;
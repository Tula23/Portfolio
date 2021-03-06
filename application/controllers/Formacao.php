<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Formacao extends CI_Controller {
	public function __construct() {
		parent::__construct();

		if(!$this->session->userdata('Dados')){
			redirect('home','refresh');
		}
	}

	private function atualizaBanco(){
		$this->load->library('migration');

		if ($this->migration->current() === FALSE){
			show_error($this->migration->error_string());
		}
	}

	public function index()	{
		$this->atualizaBanco();
		$data = array(
			'title' => 'Formação Acadêmica',
			'menu' => 'formacao',
			'stylesheets' => array(
				'template/dashboard.css'
			),
			'scripts' => array(
				'util.js',
				'formacao.js'
			),
			'modals' => array(
				'modal_formacao')
		);

		$this->template->showDashboard('formacoes', $data);
	}

	public function listar(){
		$this->load->model('formacao_model');
		$json['data'] = array();

		$resultado = $this->formacao_model->getformacoes();

		foreach ($resultado as $item) {
			foreach (get_ensino() as $key => $value){
				if($item->escolaridade == $key){
					$item->escolaridade = $value;
				}
			}

			foreach (get_nivel() as $key => $value){
				if($item->nivel == $key){
					$item->nivel = $value;
				}
			}
			array_push($json['data'], array(
				'id' => $item->id,
				'nome' => $item->nome,
				'escolaridade' => $item->escolaridade,
				'nivel' => $item->nivel,
				'inicio' => $item->inicio,
				'termino' => $item->termino,
				'mostrar_curriculo' => ($item->mostrar_curriculo == 0) ? 'NÃO' : 'SIM',
				'acao' => '<button type="button" class="btn btn-link btn-sm" onclick="javascript:editar('.$item->id.')"><i class="fas fa-edit"></i></button>&nbsp;<button type="button" class="btn btn-link btn-sm text-danger" onclick="javascript:deletar('.$item->id.')"><i class="fas fa-trash"></i></button>',
			));
		}

		echo json_encode($json);
	}

	public function visualizar($id) {

		$this->load->model('formacao_model');
		$json = $this->formacao_model->getFormacao($id);

		echo json_encode($json);
	}

	public function salvar() {
		$json['type'] = 'success';
		$json['title'] = 'Dados alterados com sucesso';

		$valor = $this->input->post();

		if (!isset($valor['nivel'])){
			$valor['nivel'] = null;
		}

		$this->load->model('formacao_model');
		$id = $this->formacao_model->setFormacao($valor);

		if($id == 0){
			$json['type'] = 'error';
			$json['title'] = 'Dados nao atualizados';
		}

		echo json_encode($json);
	}

	public function deletar($id) {
		$json['type'] = 'success';
		$json['title'] = 'Formação Acadêmica detada com sucesso';

		$this->load->model('formacao_model');
		$result = $this->formacao_model->delFormacao($id);

		if($result == 0){
			$json['type'] = 'error';
			$json['title'] = 'Formação Acadêmica não deletada';
		}

		echo json_encode($json);
	}

}
?>
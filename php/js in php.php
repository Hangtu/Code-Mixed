<?php

	class tabla_scroll{
		private $campos;
		private $unidad_dimension;
		private $ancho;
		private $alto;
		private $id;
		private $tamanio_texto_encabezado;
		private $tamanio_texto_contenido;
		public  $tr_contenido;
		public  $style;

		public function __construct($id){
			$this->campos 	= "";
			$this->unidad_dimension="";
			$this->ancho 	= "";
			$this->alto  	= "";
			$this->id 		= $id;
			$this->tr_contenido = "";
			$this->style        = "";
		}
		public function set_style($estilo=""){
			$this->estilo = $estilo;
		}
		public function set_ancho($ancho=0, $unidad="px"){
			$this->ancho = $ancho.$unidad;
		}
		public function set_alto($alto=0){
			$this->alto	 = $alto;
		}
		public function set_alto_titulo($alto_titulo=0){
			$this->alto_titulo	 = $alto_titulo;
		}
		public function set_campos($campos="",$unidad=""){
			$this->campos 			= str_replace("acute;","acute",$campos);
			$this->unidad_dimension = $unidad;
		}
		public function set_tamanio_texto_encabezados($tamanio_texto_encabezado){
			$this->tamanio_texto_encabezado = $tamanio_texto_encabezado;
		}
		public function set_tamanio_texto_contenido($tamanio_texto_contenido){
			$this->tamanio_texto_contenido = $tamanio_texto_contenido;
		}
		private function pintar_campos(){
			$lista_campos 	= explode(";",$this->campos);
			$string 		= "";
			$unid			= $this->unidad_dimension;

			foreach($lista_campos as $nombre){
				list($nombre,$dimencion)= explode(":->",$nombre);
				$string	.=	"<td style='width:$dimencion$unid; font-weight:normal' class='table_head'>".str_replace("acute","acute;",$nombre)."</td>";
			}
			return  "<thead><tr style='height:20px;".$this->get_tamanio_texto($this->tamanio_texto_encabezado).
			"' >".$string."</tr></thead>";
		}

		private function pintar_filas_vacias(){
			$lista_campos 	= explode(";",$this->campos);
			$string 		= "";
			$unid			= $this->unidad_dimension;
			foreach($lista_campos as $contenido){
				list($nombre,$dimencion)= explode(":->",$contenido);
				$string	.=	"<td style='width:$dimencion$unid;' class='table_head'></td>";
			}
			return "<thead><tr>".$string."</tr></thead>";
		}

		private function get_alto($rest=0){
			if($this->alto !="" && $this->alto  !="0")
				return " height:".($this->alto-$rest)."px;";
		}
		private function get_ancho(){if($this->ancho!="" && $this->ancho !="0")			return " width:$this->ancho;"; }
		private function get_tamanio_texto($tam){if($tam!="" && $tam!="0")				return " font-size:".$tam."px;";}
		public function display(){

			$cad = "";
			// echo $this->get_ancho();
			if(trim($this->get_ancho()) == 'width:0px;'){
				$cad = "<script>
				jQuery('.scroll_table').css({width: (screen.width - 70)});
				jQuery('.scroll_table > table').css({width: (screen.width - 70)});
				jQuery('#tabla_$this->id').css({width: (screen.width - 70)});
			  </script>";
		  }

		return "
		<div style='".$this->get_ancho()." ".$this->get_alto()." ;border-radius:3px' class='table_color'>
			<table id='tabla_".$this->id."' cellspacing='0' cellpadding='0' style=".$this->get_ancho()." $this->style>".$this->pintar_campos()."</table>
			<div style='".$this->get_ancho()." ".$this->get_alto(25)."' class='scroll_table'>
				<table cellspacing='0' cellpadding='0' style=".$this->get_ancho().">".$this->pintar_filas_vacias()."
					<tbody id='".$this->id."' style='".$this->get_tamanio_texto($this->tamanio_texto_contenido)."'>$this->tr_contenido</tbody>
				</table>
			</div>
		</div> $cad";
	}

}
?>

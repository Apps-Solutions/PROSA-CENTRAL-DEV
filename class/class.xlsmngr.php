<?php

class XlsMngr
{
    private $content;
    private $cRow;
    
    function XlsMngr()
    {
		$this->content = '';
		$this->cRow = 1;
		
		$this->xlsBOF();
    }
    
    protected function xlsBOF()
    {
		//$this->content .= pack("ssssss", 0x809, 0x8, 0x0, 0x10, 0x0, 0x0);
		$this->content .= pack("ssssss", 0x809, 0x8, 0x0, 0x10, 0x0, 0x0);
    }
	
    protected function xlsEOF()
    {
		//$this->content .= pack("ss", 0x0A, 0x00);
		$this->content .= pack("ss", 0x0A, 0x00);
    }
    
    public function xlsWriteNumber($Row, $Col, $Value)
    {
		//$this->content .= pack("sssss", 0x203, 14, $Row, $Col, 0x0);
		$this->content .= pack("sssss", 0x203, 14, $Row, $Col, 0x0);
		$this->content .= pack("d", $Value);
    }
    
    public function xlsWriteLabel($Row, $Col, $Value)
    {
		$L = strlen($Value);
		$this->content .= pack("ssssss", 0x204, 8 + $L, $Row, $Col, 0x0, $L);
		$this->content .= $Value;
    }
    
    public function set_header( $head, $headrow = 0 )
    {
		$c = 0;
		foreach($head as $col)
		{
			$this->xlsWriteLabel($headrow, $c, $col);
			$c++;
		}
    }
    
    public function insert_row( $data )
    {
		if( is_array($data))
		{
			//insertar datos
			//for($i=0; $i<count($data); $i++)
			$i = 0;
			foreach($data as $dato)
			{
				if(is_numeric($dato) )
				{
					$this->xlsWriteNumber($this->cRow, $i, $dato);
				}
				else
				{
					$this->xlsWriteLabel($this->cRow, $i, $dato);
				}
				$i++;
			}
			
			$this->cRow++;
			//incrementar el renglon
		}
    }
    
    public function finish_xls()
    {
		$this->xlsEOF();
		return $this->content;
    }
}

?>
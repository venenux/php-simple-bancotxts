<?php

/**
 * class implementation to output markdown text from class file comments
 * 
 * @author PICCORO Lenz McKAY <mckaygerhard@gmail.com>
 * @copyright Copyright (c) 2019
 * @license http://opensource.org/licenses/gpl-3.0.html GNU Public License
 */
class ClassTxtBancos 
{

	private $filename = NULL;
	private $txtheaders = NULL;
	private $arraydata = NULL;
	private $withHeader = FALSE;
	private $txtfilter = NULL;
	public $codestatus = 0;

	function __construct($filename = NULL)
	{

		if ($filename and $filename != NULL)
		{
			$this->setHasHeader(TRUE);
			$this->setFilename($filename);
		}
		$this->codestatus = 1;

	}


	public function getFilename()
	{
		return $this->filename;
	}

	public function getTxtHeaders()
	{
		return $this->headers;
	}

	public function getArraydata()
	{
		return $this->arraydata;
	}

	public function getNumRows()
	{
		$numRows = 0;
		if ($this->arraydata)
			$numRows = count($this->arraydata);
		return $numRows;
	}

	public function setHasHeader($withHeader)
	{
		if (is_bool($withHeader))
			$this->withHeader = $withHeader;
		else
			$this->withHeader = FALSE;
	}

	public function setFilename($filename, $mode = 'r')
	{

		if (file_exists($filename) AND $mode == 'r')
		{
			$this->filename = $filename;
			$this->txtReadHead();
			$this->txtReadData();
			return;
		}
		else if (file_exists($filename) AND $mode == 'w')
		{
			$this->filename = $filename;
			$this->txtWritHead();		// TODO: txt bancos casi nunca traen cabecera
			$this->txtWritData();		// TODO set txtHeadWrite(array)
			return;
		}
		else
		{
			$this->codestatus = 3;
		}
	}

		private function txtReadHead()
		{
			$handle = @fopen($this->filename, "r");
			if ($handle)
			{
				$firstRow = fgets($handle, 4096);
				if ($this->withHeader) 
				{
					$this->headers = preg_split('/(?:\s\s+|\n|\t)/', $firstRow, 0, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_OFFSET_CAPTURE);
				}
				else
				{
					$this->headers = preg_split('/(?:\s\s+|\n|\t)/', $firstRow, 0, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_OFFSET_CAPTURE);
					$numFields = count($this->headers);
					for ($x = 0; $x < $numFields; $x++) 
					{
							$this->headers[$x][0] = "Column" . ($x + 1) ;
					}
				}
				fclose($handle);
				$this->codestatus = 2;
			}
			else
			{
				$this->headers = NULL;
				$this->codestatus = 4;
			}
		}

		private function txtReadData()
		{
			$this->arraydata = array();
			$handle = fopen($this->filename, "r");
			if ($handle) 
			{
				if ($this->withHeader) 
				{
					$firstRow = fgets($handle, 4096);
				}
				while (($buffer = fgets($handle, 4096)) !== false)
				{
					$addThisLine = true;
					$fields = preg_split('/(?:\s\s+|\n|\t)/', $buffer, 0, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_OFFSET_CAPTURE);

					if ( count($fields) != count($this->headers) ) 
					{
						$this->codestatus = 6;
						return;
					}
					if ($addThisLine === true) 
					{
						$numFields = count($this->headers);
						$fieldLength = strlen($buffer);
						$rowData = Array();
						for ($x = $numFields - 1; $x >= 0; $x--) 
						{
							$fieldLength = $fieldLength - $this->headers[$x][1];
							$rowData[$this->headers[$x][0]] = rtrim(substr($buffer, $this->headers[$x][1], $fieldLength));
							$fieldLength = $this->headers[$x][1];
						}
						$this->arraydata[] = $rowData;
					}
				}
				if (!feof($handle)) 
				{
					$this->codestatus = 8;
				}
				fclose($handle);
			}
		}

	}

?>

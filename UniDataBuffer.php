<?php
namespace UniDataBuffer;

class UniDataBuffer{
	private      $SEEK = 0;
	protected    $BUFF; // data string

	public function __construct(){
		$this->BUFF = null;
	}
	public function __destruct(){
		$this->BUFF = null;
	}

        /**
         * Append DWORD (V) to buffer
         * @param type $dword
         * @return int
         */
	public function AppendDWord($dword){
		$this->BUFF .= pack('V',$dword); //unsigned long (always 32 bit, big endian byte order)
		return 1;
	}

        /**
         *  Append WORD (v) to buffer
         * @param type $word
         * @return int
         */
	public function AppendWord($word){
		$this->BUFF .= pack('v',$word);  //unsigned short (always 16 bit, big endian byte order)
		return 1;
	}

        /**
         * Append BYTE (c) to buffer
         * @param type $byte
         * @return int
         */
	public function AppendByte($byte){
		$this->BUFF .= pack('c',$byte);  //unsigned char
		return 1;
	}

        /**
         * Append buffer of type UniDataBuffer to current buffer
         * @param \UniDataBuffer\UniDataBuffer $buffer
         * @return int
         */
	public function AppendBuffer(UniDataBuffer $buffer){
		$this->BUFF .= $buffer->GetBuffer();
		return 1;
	}
        
        /**
         * Append whatever you want to buffer
         * @param type $text
         * @return int
         */
	public function AppendAnyData($text){
		$this->BUFF .=$text;
		return 1;
	}

        /**
         * Set $mixed valut to position in buffer ($type default is BYTE) 
         * 
         * @param type $posByte
         * @param type $mixed
         * @param type $type
         * @return int
         */
	public function SetAt($posByte,$mixed,$type='c'){
		$this->BUFF{$posByte} = pack($type,$mixed);
		return 1;
	}

        /**
         * Get ASCII value of character from buffer by position
         * @param type $pos
         * @return type
         */
	public function GetAt($pos){
		return ord($this->BUFF{$pos});
	}


	public function GetBuffer(){
		return $this->BUFF;
	}

        /**
         * Get buffer size
         * @return type
         */
	public function GetSize(){
		return strlen($this->BUFF);
	}
        
        /**
         * Read DWORD. Seek pointer will increment to length of DWORD (4 bytes).
         * @return type
         */
	public function ReadDWord(){
		$Byte_3 = ord($this->BUFF{$this->SEEK}); $this->SEEK++;
		$Byte_2 = ord($this->BUFF{$this->SEEK}); $this->SEEK++;
		$Byte_1 = ord($this->BUFF{$this->SEEK}); $this->SEEK++;
		$Byte_0 = ord($this->BUFF{$this->SEEK}); $this->SEEK++;
		$dw = (int)($Byte_0 << 24) | ($Byte_1 << 16) | ($Byte_2 << 8) | $Byte_3;
		return $dw;
	}
        
        /**
         * Read WORD. Seek pointer will increment to length of WORD (2 bytes).
         * @return type
         */
	public function ReadWord(){
		$Byte_1 = ord($this->BUFF{$this->SEEK}); $this->SEEK++;
		$Byte_0 = ord($this->BUFF{$this->SEEK}); $this->SEEK++;
		$w = (int)($Byte_0 << 8) | $Byte_1;  //fix
		return $w;
	}
        
        /**
         * Read BYTE. Seek pointer will increment to length of BYTE.
         * @return type
         */
	public function ReadByte(){
		$Byte_0 = ord($this->BUFF{$this->SEEK});$this->SEEK++;
		return $Byte_0;
	}
        
        
        /**
         * Read data from buffer, start from SEEK value 
         * @param type $DataLength
         * @return boolean
         */
	public function ReadData($DataLength){
		$strlen = strlen($this->BUFF);
		if ($strlen < $this->SEEK + $DataLength) //array boundary check
			return FALSE;		
		$result = substr($this->BUFF,$this->SEEK,$DataLength);
		$this->SEEK += $DataLength;
		return $result;
	}

        /**
         * Read data from SEEK to end
         * @return type
         */
	public function ReadDataToEnd(){
		return substr($this->BUFF,$this->SEEK);
	}
		
        /**
         * Check for unexpected end of buffer, otherwise skip read data in size $dwSize
         * @param type $dwSize
         * @return boolean
         */
	public function SkipRead($dwSize){
		if (strlen($this->BUFF) < $this->SEEK + $dwSize)
			return FALSE;
		$this->SEEK += $dwSize;
		return TRUE;
	}
	
        /**
         * Reset SEEK poiner
         */
	public function ResetSeek()
	{
		$this->SEEK = 0;
	}
        
        
        /**
         * Set SEEK pointer to start read data from.
         * @param type $SEEK
         * @return boolean
         */
	public function SetSeek($SEEK)
	{
		if (strlen($this->BUFF) < $SEEK)
			return FALSE;		
		$this->SEEK = $SEEK;
	}
        
        /**
         * Get SEEK poiter value
         * @return type
         */
	public function GetSeek()
	{
		return $this->SEEK;
	}
        
        
        /**
         * Use in code in case of testing and debuging
         */
	public function DebugPrint()
	{
		$OriginalSeekSaved = $this->SEEK;
		$BufLen = strlen($this->BUFF);
		$this->SEEK = 0;
		for($i = 0;$i < $BufLen;$i++)
		{
			$b = $this->ReadByte();
			printf("%02X",$b);
		}
		$this->SEEK = $OriginalSeekSaved;
	}
}
?>
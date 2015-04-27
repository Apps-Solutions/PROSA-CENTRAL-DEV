<?php
/**
* User Profile
* 
* @package		Meta Tracker			
* @since        18/05/2014
* @author		Manuel Fernández
*/ 

class Validate{
	
	function Validate(){
		
	}
	
	public function clean_input( $string ){
		$string = strip_tags(trim( $string ));
		
		$search = array(
		    '@<script[^>]*?>.*?</script>@si',   // Strip out javascript
		    '@<[\/\!]*?[^<>]*?>@si',            // Strip out HTML tags
		    '@<style[^>]*?>.*?</style>@siU',    // Strip style tags properly
		    '@<![\s\S]*?--[ \t\n\r]*>@'         // Strip multi-line comments
		  ); 
	    $string = preg_replace($search, '', $string); 
		$string = htmlspecialchars($string); 
		return $string;
	}
	
	/**
	* is_number()    
	* Validates if input is a HEX color
	*  
	* @param	$val 
	*  
	* @return 	TRUE if $val is a HEX color; FALSE otherwise
	*/ 
	public function is_color( $val ){ 
		return (preg_match('/^#[a-f0-9]{6}$/i', $val)); //hex color is valid 
	}
	
	/**
	* is_number()    
	* Validates if input is a number
	*  
	* @param	$val 
	*  
	* @return 	TRUE if $val is a numer; FALSE otherwise
	*/ 
	public function is_number( $val ){
		return is_numeric( $val );
	}
	
	/**
	* is_integer()    
	* Validates if input is integer
	*  
	* @param	$val 
	*  
	* @return 	TRUE if $val is a Integer; FALSE otherwise
	*/ 
	public function is_integer( $val ){ 
		return ctype_digit($val);
	}
	
	/**
	* is_email()    
	* Validates if input is an email
	*  
	* @param	$string String  
	*  
	* @return 	TRUE if $string is an email; FALSE otherwise
	*/ 
	public function is_email( $string ){
		return filter_var($string, FILTER_VALIDATE_EMAIL); 
	}
	
	/**
	* is_ip()    
	* Validates if input is an IP
	*  
	* @param	$string String
	*  
	* @return 	TRUE if $string is an IP; FALSE otherwise
	*/ 
	public function is_ip( $string ){
		return filter_var($string, FILTER_VALIDATE_IP);
	}
	
	/**
	* is_url()
	* Validates if input is an URL
	*  
	* @param	$string String
	*  
	* @return 	TRUE if $string is an URL; FALSE otherwise
	*/ 
	public function is_url( $string ){
		return filter_var($string, FILTER_VALIDATE_URL);
	}
	
	/**
	* is_int_between()    
	* Validates if input is an between two values
	*  
	* @param	$val
	* @param	$min
	* @param	$max 
	*  
	* @return 	TRUE if $val is between $min and $max; FALSE otherwise
	*/ 
	public function is_int_between( $val, $min, $max){
		return filter_var($val, FILTER_VALIDATE_INT, 
						array(
							    'options' => array(
							                      'min_range' => $min,
							                      'max_range' => $max,
							                      )) 
								);
	} 
	
	/**
	* is_unique()    
	* Validates if value is unique in DB table column. 
	*  
	* @param	$table (Haystack table) 
	* @param	$column (Column of table)
	* @param	$value	(Value to look for)
	* @param	$id_col (Name of the ID column to exclude from query)
	* @param	$id_val (Value of the id to exclude from query)
	*  
	* @return 	TRUE if $val is between $min and $max; FALSE otherwise
	*/ 
	public function is_unique( $table, $column, $value, $id_col, $id_val ){
		global $obj_bd;
		$query = "SELECT " . $column 
				. " FROM " . PFX_MAIN_DB . $table 
				. " WHERE " . $column . " = '" . $value . "' " 
					. " AND NOT " . $id_col . " = '" . $id_val . "' ";
		$result = $obj_bd->query( $query );
		if ( $result !== FALSE ){
			if ( count($result)  > 0 ){
				return FALSE;
			} else {
				return TRUE;
			}
		} else {
			return FALSE;
		}
	}
	
	/**
	* exists()    
	* Validates if value exists on the DB.  
	*  
	* @param	$table (Haystack table) 
	* @param	$column (Column of table)
	* @param	$value	(Value to look for) 
	*  
	* @return 	TRUE if $val is between $min and $max; FALSE otherwise
	*/ 
	public function exists( $table, $column, $value ){
		global $obj_bd;
		$query = "SELECT " . $column 
				. " FROM " . PFX_MAIN_DB . $table 
				. " WHERE " . $column . " = '" . $value . "' " ;
		$result = $obj_bd->query( $query );
		if ( $result !== FALSE ){
			if ( count($result)  > 0 ){
				return TRUE;
			} else {
				return FALSE;
			}
		} else {
			return FALSE;
		} 
	}
	
	/**
	 * uploaded_is_image()
	 * Validates if uploaded file is image with valid $size, $width & $height 
	 * 
	 * @param 	$file uploaded file
	 * @param 	$size (optional) Size in bits default to 500KB
	 * @param	$width (optional) Width for the image to validate; 0 if no width validation
	 * @param	$height (optional) Width for the image to validate; 0 if no height validation
	 * 
	 * @return 	TRUE if valid; FALSE otherwise
	 */
	 public function uploaded_is_image( $file, $size = 5000000, $width = 0, $height = 0 ){
	 	if ( $file['name'] != '' && $file['tmp_name'] != ''){
	 		$tmp = $file['tmp_name']; 
		    if (file_exists($tmp)) {
		        $imagesizedata = getimagesize($tmp);
		        if ($imagesizedata === FALSE) {
		            return "Not an image.";
		        }
		        else { 
					/*TODO: properties validation*/
					$allowedExts = array("gif", "jpeg", "jpg", "png");
					$extension = end(explode(".", $file["name"]));
					if ((  ( 	$file["type"] == "image/gif")
							|| ($file["type"] == "image/jpeg")
							|| ($file["type"] == "image/jpg")
							|| ($file["type"] == "image/png")
							) 
					){
						
						if ( $size < $file["size"] ){
							return "File size larger than " . $size . " (" . $file["size"] . ")";
						}
						else {
							return TRUE;
						}
					} 
					else {
						return "Invalid image format.";
					}
		        }
		    }
		    else {
		        return "No image found.";
		    } 
	 	} 
	 	else return FALSE;
	 } 
	 
}
?>
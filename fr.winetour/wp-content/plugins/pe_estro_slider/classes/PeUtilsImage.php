<?php
	// plugin admin class 
	class PeUtilsImage {
		function getThumb($img,$width,$height,$crop) {
		
			$paths = wp_upload_dir();
			$img = str_replace($paths["baseurl"],"",$img);
			$img = $paths['basedir'].$img;
			
			if (!$img || !($target = @filemtime($img))) return false;
							
 			$info = pathinfo($img);
			$dir = $info['dirname'];
			$ext = $info['extension'];
			
			
			$thumb = "$dir/".wp_basename($img, ".$ext")."-{$width}x{$height}.{$ext}";
			if (!($dest = @filemtime($thumb)) || $dest < $img) {
				$thumb = image_resize($img,$width,$height,$crop);
			}
			return $thumb;
					
		}
	}
?>
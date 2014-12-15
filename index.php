<?php
define('_ALLOWINCLUDE',0);
include 'settings.php';
/**
 *  A script to download files from MediaFile and
 *  upload them to the script host server.
 *  Written by Mohammed Fadin
 *  2009/2010
 */
if (function_exists('curl_init'))
{
	$snatch_system = 'curl';
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
<link rel="stylesheet" type="text/css" href="style.css">
<title>Mediafire File copier</title>
</head>
<body>

<div id="main">
<h3 style="text-align: center;font-size: 35px;">Anime Gate Special Uploader Script v1.0</h3>
<h4 style="text-align:center;">Programmed by Mohammed Fadin </4>
<?php
$submit = $_POST['submit'];
if ($submit)
{
	if (isset($password))
	{
		if ($_POST['password'] != $password)
		{
			die('<p><strong>Password incorrect!</strong></p>');
			$error = true;
		}
	}

	if (!$defaultDest)
	{
		$defaultDest = 'snatched';
	}

	if (!file_exists($defaultDest))
	{
		mkdir($defaultDest);
	}

	$sizelimit = $sizelimit * 1024;

	$files = $_POST['file'];
	$news = $_POST['new'];
	$allfiles = $_POST['allfiles'];
	$separateby = $_POST['separateby'];
	if($allfiles != "")
	{
		$files = explode($separateby,$allfiles);
	}
	for($i=0;$i<count($files);$i++)
	{

		$file = trim($files[$i]);
		$uploadfile = explode('/', $file);
		$filename = array_pop($uploadfile);

		$newfilename = $news[$i];

		if (!$newfilename)
		{
			$newfilename = $filename;
		}

		if (!isset($file))
		{
			echo '<p><strong>Please enter a URL to retrieve file from!</strong></p>';
			$error = true;
		}

		if (!isset($newfilename))
		{
			echo '<p><strong>Please enter a new file name!</strong></p>';
			$error = true;
		}

		if ($error == false)
		{
			$dest = $defaultDest;
			$ds = array($dest, '/', $newfilename);
			$ds = implode('', $ds);
			$newname_count = 0;
			if (file_exists($ds))
			{
				echo '<p><strong>File already exists!</strong></p>';
				$newname_count++;
				$newfile = array($newname_count, $newfilename);
				$newfile = implode('~', $newfile);
				$newfile_ds = array($dest, '/', $newfile);
				$newfile_ds = implode('', $newfile_ds);
				while($renamed == false)
				{
					if (file_exists($newfile_ds))
					{
						$newname_count++;
						$newfile = array($newname_count, $newfilename);
						$newfile = implode('~', $newfile);
						$newfile_ds = array($dest, '/', $newfile);
						$newfile_ds = implode('', $newfile_ds);
					}
					else
					{
						$renamed = true;
					}
				}
				$newfilename = $newfile;
				$ds = $newfile_ds;
				echo '<p>New file name is <strong>'.$newfile.'</strong>.</p>';
			}
			echo '<p><strong>Copying...</strong></p>';
			if ($snatch_system == 'curl')
			{
				$ch = curl_init($file);
				$fp = fopen($ds, 'w');
				curl_setopt($ch, CURLOPT_FILE, $fp);
				curl_setopt($ch, CURLOPT_HEADER, 0);
				curl_exec($ch);
				$curl_info =  curl_getinfo($ch);
				curl_close($ch);
				fclose($fp);
			}
			else
			{
				if (!copy($file, $ds))
				{
					echo '<p>Was unable to copy <a href="'.$file.'">'.$file.'</a><br />See if your path and destination are correct.</p>';
					$copy_fail = true;
				}
			}

			if ($copy_fail == false)
			{
				if ($sizelimit > 0 && filesize($ds) > $sizelimit)
				{
					echo '<p><strong>File is too large.</strong>';
					unlink($ds);
				}
				else
				{
					echo '<p style="font-size:18px;"><strong>Copy successful!</strong></p>';
					echo '<p><a href="'.$URLDest.'/'.$newfilename.'">Click here for file</a></p>';
					echo '<br>';
					echo '<div onclick="this.focus(); this.select()"><h3> [videoplayer file="'.$URLDest.'/'.$newfilename.'" width="658" height="494" /]</h3> </div>';
					echo '<br>';
					if ($snatch_system == 'curl')
					{
						$size_dl = round($curl_info['size_download']/1024, 2);
						$speed_dl = round($curl_info['speed_download']/1024, 2);
						echo '<p>Downloaded '.$size_dl.'KB in '.$curl_info['total_time'].' seconds.<br />With an average download speed of '.$speed_dl.'KB/s.';
					}
				}
			}
		}

	}
}

$self = $_SERVER['PHP_SELF'];
?>

<fieldset><legend>Mediafire file copier</legend>
<?
	$repeat = (isset($_REQUEST['repeat']))?($_REQUEST['repeat']):(1);
?>
<form method="POST" action="<?=$self?>">
<input type="text" name="repeat"  size="10" value="<?=$repeat;?>">
<input name="Repeat" value="Repeat" type="submit">
</form>

<form method="POST" action="<?=$self?>">
<? for($i=0;$i<$repeat;$i++){?>
<br>File<?=($i+1)?> : <input type="text" name="file[]"  size="45" value="">
<!--<input type="text" name="new[]" size="45" value="">-->
<? } ?>

<br>OR<br><br>
<textarea name="allfiles" cols="36" rows="10"></textarea>
Separate URL by:
<input type="text" value="##" name="separateby"  size="5" value="<?=$separateby;?>">


<? if (isset($password)){ ?>

	<label for="password">Password</label>
	<input type="password" name="password" id="password" size="45" value=""><br />
<? } ?>
<p><input name="submit" type="submit" id="submit" value="submit" accesskey="s"></p>
</form>
</fieldset>

</div>
</body>
</html>

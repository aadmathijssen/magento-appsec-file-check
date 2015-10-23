<?php

// PATH TO MAGENTO ROOT
//
$_magentoPath='/home/www/ecommerce/shop01/magento/';


// SECURITY NOTICES AND QUERIES
//
$_securityNotices=array(
	'APPSEC-1034, addressing bypassing custom admin URL' => array(
		'grep' => array(
			'grep -irl "<use>admin</use>"'. ' '. $_magentoPath. 'app/code/*'
		),
		'magentopath' => $_magentoPath),
	'APPSEC-1063, addressing possible SQL injection' => array(
		'grep' => array(
			'grep -irl "collection->addFieldToFilter(\'"'. ' '. $_magentoPath. 'app/code/community/*',
			'grep -irl "collection->addFieldToFilter(\'"'. ' '. $_magentoPath. 'app/code/local/*',
			'grep -irl "collection->addFieldToFilter(\'\`"'. ' '. $_magentoPath. 'app/code/community/*',
			'grep -irl "collection->addFieldToFilter(\'\`"'. ' '. $_magentoPath. 'app/code/local/*'			
		),
		'magentopath' => $_magentoPath),
	'APPSEC-1057, template processing method allows access to private information' => array(
		'grep' => array(
			'grep -irl "{{config path="'. ' '. $_magentoPath. 'app/code/community/*',
			'grep -irl "{{block type="'. ' '. $_magentoPath. 'app/code/local/*'
		),
		'magentopath' => $_magentoPath)		
);

echo '*** Magento security file check ***'. "\n";
$_count=1;

foreach ($_securityNotices as $_name => $_securityNotice)
{

	echo '['. $_count++. '] '. $_name. "\n";
	echo doExec($_securityNotice)."\n\n";

}

echo '***********************************'. "\n";
exit;


function doExec($_securityNotice)
{
	$_text='';	
	
	foreach ($_securityNotice['grep'] as $_grep)
	{
		$_exec=$_grep;
		$_count=0;
		$_search='';
		
		exec($_exec, $_output, $_status);
		preg_match('/"([^"]+)"/', $_grep, $_query);
		
		if (1 === $_status)
		{
			
			$_text=$_text.$_query[1]. ' not found.'. "\n";
			continue;
		}

		if (0 === $_status)
		{
			$_count=count($_output);
			
			foreach ($_output as $_line)
			{
				$_search=$_search.$_query[1]. ' found in '. str_replace($_securityNotice['magentopath'],' ', $_line). "\n";
			}
			
		} else {
			$_text=$_text. 'Command '. $_grep. ' failed with status: ' . $_status. "\n";
		}
		
		$_text=$_text.$_count. ' effected files : '. "\n". $_search;
	}
	
	return $_text;
	
}

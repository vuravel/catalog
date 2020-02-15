<?php 

use Illuminate\Support\Str;

if (! function_exists('catalog')) {
	function catalog($catalog, $dontBoot = false)
	{
		$catalog = ucfirst(Str::camel($catalog));
		if($catalogClass = catalogExists($catalog)){
			return new $catalogClass($dontBoot);
		}else{
			abort(404, 'Class App\\Catalogs\\'.$catalog.' not found');
		}
	}
}

if (! function_exists('catalogExists')) {
	function catalogExists($catalog)
	{
		if(class_exists('App\\Catalogs\\'.$catalog)){
			return 'App\\Catalogs\\'.$catalog;
		}elseif(class_exists('Vuravel\\Catalog\\Catalogs\\'.$catalog)){
			return 'Vuravel\\Catalog\\Catalogs\\'.$catalog; //useless, no forms exist...
		}else{
			return false;
		}
	}
}

if (! function_exists('catalogStore')) {
	function catalogStore($catalog, $store)
	{
		return catalog($catalog, true)->store($store);
	}
}
<?php

namespace DreamTeam\Base\Supports;

use DreamTeam\Base\Models\BaseModel;
use Exception;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Request;

class MenuStore
{
	public array $menus;
	public array $locations;

	public function __construct()
    {
        $this->menus = [];
        $this->locations = [];
    }

	public function registerMenu(array $module): self
	{
		if (! is_in_admin()) {
            return $this;
        }
        if(isset($module['id'])) {
			$this->menus[$module['id']] = $module;
		}
		return $this;
	}

	public function getAll()
	{	
		$menus = $this->menus;
		return collect($menus)->sortBy('priority');
	}

	public function registerLocation(array $location): self
	{
		if (! is_in_admin()) {
            return $this;
        }
        if(isset($location['id'])) {
			$this->locations[$location['id']] = $location;
		}
		return $this;
	}

	public function getLocations()
	{	
		$locations = $this->locations;
		$locations = collect($locations)->sortBy('priority');
		return $locations->pluck('name', 'id')->toArray();
	}

}
<?php

namespace DreamTeam\Asset\MyClass;

class Asset {

	protected $config;

	/*
	 * mảng script
	 */
	protected $scripts = [];

	/*
	 * mảng style
	 */
	protected $styles = [];

	function __construct()
	{
		$this->config = config('dreamteam_asset');

		$this->addDefault('styles');
		$this->addDefault('scripts');
	}

	// Lấy toàn bộ style và đưa vào mảng
	public function getStyles() {
		return $this->styles;
	}

	public function getScripts() {
		return $this->scripts;
	}

	/**
	 * Nhận key styles truyền vào
	 * @param string or array 	$assets: key assets truyền vào, lấy key tại config dreamteam_asset.resources.styles
	 * @return $this
	 */
	public function addStyle($assets) {
		$config_resource = $this->config['resources']['styles'];
		foreach ((array)$assets as $value) {
			$config = $config_resource[$value];
			$default = [
				'key' => $value,
				'location' => $config['location'] ?? 'top',
				'attributes' => $config['attributes'] ?? [],
			];
			if (config('dreamteam_asset.offline') == false && $config['use_cdn'] == true) {
				$default['src'] = $config['src']['cdn'] ?? null;
			} else {
				$default['src'] = $config['src']['local'] ?? null;
			}
			$this->styles[] = $default;
		}
		return $this;
	}

	/**
	 * Nhận key scripts truyền vào
	 * @param string or array 	$assets: key assets truyền vào, lấy key tại config dreamteam_asset.resources.scripts
	 * @return $this
	 */
	public function addScript($assets) {
		$config_resource = $this->config['resources']['scripts'];
		foreach ((array)$assets as $value) {
			$config = $config_resource[$value];
			$default = [
				'key' => $value,
				'location' => $config['location'] ?? 'top',
				'attributes' => $config['attributes'] ?? [],
			];
			if (config('dreamteam_asset.offline') == false && $config['use_cdn'] == true) {
				$default['src'] = $config['src']['cdn'] ?? null;
			} else {
				$default['src'] = $config['src']['local'] ?? null;
			}
			$this->scripts[] = $default;
		}
		return $this;
	}

	/**
	 * Nhận đường dẫn trực tiếp truyền vào
	 * @param string or array 	$assets: Đường dẫn hoặc mảng đường dẫn truyền vào
	 * @param string 			$asset_type: Loại file [styles | scripts]
	 * @param string 			$location: vị trí cho toàn bộ đường dẫn
	 * @param array 			$attributes: thuộc tính cho toàn bộ đường dẫn
	 * @return $this
	 */
	public function addDirectly($assets, $asset_type, $location = 'top', $attributes = []) {
		foreach ((array)$assets as $value) {
			$this->$asset_type[] = [
				'key' => 'custom',
				'location' => $location,
				'attributes' => $attributes,
				'src' => $value,
			];
		}
		return $this;
	}

	/**
	 * Lấy ra các cấu hình load cho toàn trang
	 * @param string			$asset_type: Loại file [styles | scripts]
	 * @return $this
	 */
	public function addDefault($asset_type) {
		$style_default = $this->config[$asset_type];
		$config_resource = $this->config['resources'][$asset_type];
		foreach ($style_default as $value) {
			if ($asset_type == 'styles') {
				$this->addStyle($value);
			} else if ($asset_type == 'scripts') {
				$this->addScript($value);
			}
		}
		return $this;
	}

	/**
	 * Xóa toàn bộ style theo resource key
	 * @param string or array 	$assets: key assets truyền vào, lấy key tại config dreamteam_asset.resources.styles
	 * @return $this
	 */
	public function removeStyle($assets) {
		foreach ((array)$assets as $value) {
			foreach ($this->styles as $key => $style) {
				if ($value == $style['key']) {
					unset($this->styles[$key]);
				}
			}
		}
		return $this;
	}

	/**
	 * Xóa toàn bộ script theo resource key
	 * @param string or array 	$assets: key assets truyền vào, lấy key tại config dreamteam_asset.resources.scripts
	 * @return $this
	 */
	public function removeScript($assets) {
		foreach ((array)$assets as $value) {
			foreach ($this->scripts as $key => $style) {
				if ($value == $style['key']) {
					unset($this->scripts[$key]);
				}
			}
		}
		return $this;
	}

    /**
     * Sinh html style và script hiển thị ở head
     * @return string
     */
	public function renderHeader() {
		$styles = $this->buildHtmlStyle('top');
		$scripts = $this->buildHtmlScript('top');
		return view('Asset::header', compact('styles', 'scripts'))->render();
	}

	/**
     * Sinh html style và script hiển thị ở cuối thẻ body
     * @return string
     */
	public function renderFooter() {
		$styles = $this->buildHtmlStyle('bottom');
		$scripts = $this->buildHtmlScript('bottom');
		return view('Asset::footer', compact('styles', 'scripts'))->render();
	}
	
	/**
	 * Lấy mảng style tại vị trí cụ thể
	 * @param string 			$location: vị trí [top | bottom]
	 * @return @array
	 */
	public function getStyleLocation($location) {
		return collect($this->getStyles())->where('location', $location)->toArray();
	}

	/**
	 * Lấy mảng script tại vị trí cụ thể
	 * @param string 			$location: vị trí [top | bottom]
	 * @return @array
	 */
	public function getScriptLocation($location) {
		return collect($this->getScripts())->where('location', $location)->toArray();
	}

	/**
	 * Tạo chuỗi style cho vị trí cụ thể
	 * @param string 			$location: vị trí [top | bottom]
	 * @return @array
	 */
	public function buildHtmlStyle($location) {
		$array_style = $this->getStyleLocation($location);
		$str = '';
		foreach ($array_style as $value) {
			// Phải có src mới tạo link
			if (isset($value['src']) && !empty($value['src'])) {
				// Nếu không có thuộc tính rel thì tự thêm
				if (!isset($value['attributes']['rel'])) {
					$value['attributes']['rel'] = 'stylesheet';
				}
				// Chuỗi link
				$str .= '<link ';
				foreach ($value['attributes'] as $key => $attr) {
					$str .= $key.'="'.$attr.'" ';
				}
				$str .= 'href="'.$value['src'];
				if ($this->config['enable_version'] == true && !empty($this->config['version']) ) {
					$str .= '?v='.$this->config['version'];
				}
				$str .= '"';
				$str .= '>';
			}
		}
		return $str;
	}

	/**
	 * Tạo chuỗi script cho vị trí cụ thể
	 * @param string 			$location: vị trí [top | bottom]
	 * @return @array
	 */
	public function buildHtmlScript($location) {
		$array_script = $this->getScriptLocation($location);
		$str = '';
		foreach ($array_script as $value) {
			// Phải có src mới tạo link
			if (isset($value['src']) && !empty($value['src'])) {
				$str .= '<script ';
				foreach ($value['attributes'] as $key => $attr) {
					$str .= $key.'="'.$attr.'" ';
				}
				$str .= 'src="'.$value['src'];
				if ($this->config['enable_version'] == true && !empty($this->config['version']) ) {
					$str .= '?v='.$this->config['version'];
				}
				$str .= '"';
				$str .= '></script>';
			}
		}
		return $str;
	}
}
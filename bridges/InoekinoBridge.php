<?php
class InoekinoBridge extends BridgeAbstract {
	const NAME			= 'Иноекино';
	const URI			= 'https://inoekino.com/';
	const DESCRIPTION	= 'Возвращает элементы со страницы Кинопрокат';
	const PARAMETERS	= array(
		'Кинопрокат' => array(),
	);
	const CACHE_TIMEOUT	= 3600;

	public function getURI() {
		return self::URI . 'distribution';
	}

	public function getName() {
		if ($this->queriedContext) {
			return 'Кинопрокат | ' . self::NAME;
		}
		return self::NAME;
	}


	public function collectData() {
		$html = getSimpleHTMLDOM($this->getURI()) or returnServerError('Could not request ' . $this->getURI());

		foreach($html->find('.bg-gray-light') as $line) {
			$uri = $line->find('a', 0)->href;
			$title = $line->find('h2', 0)->plaintext;
			$content = $line->find('h5', 0)->plaintext;

			$item['uri']		= self::URI . $uri;
			$item['title']		= $title;
			$item['content']	= $content;
			$this->items[] = $item;
		}
	}
}

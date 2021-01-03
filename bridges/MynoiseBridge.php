<?php
class MynoiseBridge extends BridgeAbstract {
	const NAME = 'myNoise bridge';
	const URI = 'https://mynoise.net';
	const DESCRIPTION = 'Returns feeds for mynoise.net';
	const MAINTAINER = 'Amatis-54';
	const CACHE_TIMEOUT = 43200;
	const PARAMETERS = array( array(
			'choice' => array(
				'name' => 'Area',
				'type' => 'list',
				'defaultValue' => 'noises',
				'values' => array(
					'New Noises' => 'noises',
					'Blog' => 'blog',
					'Sampling Sessions' => 'sampling'
					)
				))
			);

public function collectData() {

	if ($this->getInput('choice') == 'noises') {
		$html = getSimpleHTMLDOM(self::URI)
		or returnServerError('Could not connect to myNoise.');

		$update = $html->find('div[class="LslideText"] span');
		foreach ($update as $element) {
			$item['title'] = $element->next_sibling()->plaintext;
			$item['content'] = substr('Added ' . $element->plaintext,
			0, strpos($element->innertext, '&bull') + 5);

			$item['uri'] = $element->next_sibling()->href;
			$this->items[] = $item; }

	} elseif ($this->getInput('choice') == 'blog') {
		$html = getSimpleHTMLDOM('https://mynoise.net/blog.php')
		or returnServerError('Could not connect to myNoise');

		$update = $html->find('div[class="allContent"] div[class="content"]');
		foreach ($update as $element) {
			$item['title'] = substr($element->find('p', 0)->plaintext,
			0, strpos($element->find('p', 0)->plaintext, '&bull'));

			$item['content'] = substr($element->find('p', 0)->plaintext,
			strpos($element->find('p', 0)->plaintext, '&bull') + 7);

			$item['uri'] = 'https://mynoise.net/blog.php';
			$this->items[] = $item; }

	} elseif ($this->getInput('choice') == 'sampling') {
		$html = getSimpleHTMLDOM('https://mynoise.net/SamplingSessions/index.php')
		or returnServerError('Could not connect to myNoise');

		$update = $html->find('div[class="allContent"] div[class="content"]');
		foreach ($update as $element) {
			$item['title'] = $element->find('h1', 0)->plaintext;
			$item['content'] = substr($element->find('p', 0)->innertext,
			0, strpos($element->find('p', 0)->innertext, '<br><br>'));

			$item['uri'] = 'https://mynoise.net/SamplingSessions/' .
			substr($element->find('a', 0)->getAttribute('href'), 2);

			$this->items[] = $item; }
	}
}
}

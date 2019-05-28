document.addEventListener('DOMContentLoaded', (ev) => {
	Prism.plugins.NormalizeWhitespace.setDefaults({
		'spaces-to-tabs': 4,
	});

	document.querySelectorAll('code.github-embed').forEach((el) => {
		var url = el.getAttribute('src');
		fetch(url)
		.then(async (resp) => {
			var text = await resp.text();
			//text = text.replace(/^\t*/gm, m => m.replace(/\t/g, "    "));
			el.textContent = text;
			Prism.highlightElement(el);
		});
	});

	document.querySelectorAll('.toggler, .revealer').forEach($toggler => {
		$toggler.querySelector('.banner').addEventListener('click', ev => {
			var $content = $toggler.querySelector('.content');
			$toggler.classList.toggle('open');
			$toggler.classList.toggle('closed');
		});
	});
});

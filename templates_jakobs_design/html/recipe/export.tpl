{use $model, $recipe = $model->recipe, $root = $model->request->root}
{tr_context "recipes"}
<div class="page">
	<h2>{tr "Export %recipe" vars "recipe" => $recipe->title}</h2>

	<ul class="exports">
		<li><a href="{$root}/{$model->request->controller}/export/{$recipe->id}?format=.pdf"><img width="16" height="16" src="{$root}/images/application-pdf.png" alt="application/pdf" />{tr "Export as PDF"}</a></li>
		<li><a href="{$root}/{$model->request->controller}/export/{$recipe->id}?format=.html"><img width="16" height="16" src="{$root}/images/text-html.png" alt="text/html" />{tr "Export as HTML"}</a></li>
		<li><a href="{$root}/{$model->request->controller}/export/{$recipe->id}?format=.odt"><img width="16" height="16" src="{$root}/images/application-vnd.oasis.opendocument.text.png" alt="application/vnd.oasis.opendocument.text" />{tr "Export as OpenDocument"}</a></li>
		<li><a href="{$root}/{$model->request->controller}/export/{$recipe->id}?format=.txt"><img width="16" height="16" src="{$root}/images/text-plain.png" alt="text/plain" />{tr "Export as ReStructured Text"}</a></li>
		<li><a href="{$root}/{$model->request->controller}/export/{$recipe->id}?format=.xml"><img width="16" height="16" src="{$root}/images/text-xml.png" alt="text/xml" />{tr "Export as Docbook XML"}</a></li>
	</ul>
</div>

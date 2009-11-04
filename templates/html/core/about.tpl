{use $model}
<h2>Arbit - project tracking</h2>

<p>
	Arbit is a extendable issue tracking system. More information is available
	on the project homepage: 
	<a href="http://arbitracker.org/">http://arbitracker.org/</a>
</p>

<p>
	This is arbit in version {$model->revision}.
</p>

<h3>Authors</h3>

<p class="authors">
	{raw arbit_simple_markup($model->authors)}
</p>

<h3>Projects</h3>

<p>
    Arbit uses libraries from external projects and the authors of arbit want
    to thank them for their work:
</p>

<ul>
    <li>
        <h4><a href="http://ezcomponents.org/">eZ Components</a></h4>
        <p>
            eZ Components provide an amazing set of high quality PHP components
            which are used inside of arbit. This made developing arbit much
            easier and facilitated the overall development. Arbit currently uses
            the following components: Authentification, Configuration,
            ConsoleTools, Document, EventLog, Graph, Mail, MvcTools, Template
            &amp; Translation.
        </p>
    </li>
    <li>
        <h4><a href="http://www.symfony-project.org/">symfony</a></h4>
        <p>
            The symfony project provides the data for the internationalization
            of dates, which made it possible to easily translate date and time
            information into a very broad set of languages.
        </p>
    </li>
    <li>
        <h4><a href="http://arbitracker.org/phpillow.html">PHPillow</a></h4>
        <p>
            Even associated with arbit, PHPillow is an independent library to
            access CouchDB and currently is used to implement the primary
            storage backend for Arbit. We would like to especially thank the
            participants in the PHPillow project not involved with Arbit
            otherwise.
        </p>
    </li>
</ul>

<h3>License</h3>

<p class="license">
	{raw arbit_simple_markup($model->license)}
</p>


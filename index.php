<?php


$path = getcwd();

echo <<<EOL

<link href="client/css/espo/hazyblue-vertical.css" rel="stylesheet" id='main-stylesheet'>

<body style="padding: 20px 10px 60px 10px; max-width: 900px; margin: 0 auto">

<p>
<strong>You need to configure your webserver in order to being able to run EspoCRM. After that,
refresh the page.</strong>
</p>

<h2>For Apache webserver</h2>

<p>
You need to have <strong>mod_rewrite</strong> enabled. You can do it by running in the terminal:
</p>

<pre>
<code>
sudo a2enmod rewrite
sudo service apache2 restart
</code>
</pre>

<h3>Non-production environment</h3>

<p>
You need to enable `.htaccess` usage in the apache configuration. Add the code:
</p>

<pre>
<code>
&ltDirectory $path>
  AllowOverride All
&lt/Directory>
</code>
</pre>

<h3>Production environment</h3>

<p>
It's recommended to configure the document root to look at the `public`
directory and create an alias for the `client` directory. The code to add to the apache configuration:
</p>

<pre>
<code>
DocumentRoot $path/public/
Alias /client/ $path/client/
</code>
</pre>

<p>
And allow override for the `public` directory:
</p>

<pre>
<code>
&ltDirectory $path/public/>
  AllowOverride All
&lt/Directory>
</code>
</pre>

<p>
<strong>
See more details in the <a href="https:
</strong>
</p>

<h2>For Nginx webserver</h2>

<p>
You need to configure the document root to look at the `public` directory and create an alias for the `client` directory.
</p>

<p>
<strong>
See more details in the <a href="https:
</strong>
</p>

</body>

EOL;

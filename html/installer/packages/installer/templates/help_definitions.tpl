<div class="app-head">Definitions</div>
<div class="app-content">
		<p>Some definitions (according to Clay)</p>
		<h4>API</h4>
		Application Programming Interface. APIs are used to allow developers to reuse code more effectively and provide reusable functionality. APIs in Clay Framework are called Libraries.
		Applications using Clay's (or the Installer's) Application API are allowed to provide their own APIs as well.
		<h4>Application</h4>
		An application is a tool that provides a focused set of features to the user. In Clay Framework, an application uses the Clay Application API. The Application API is the basis of
		Clay's Application and Theme system. Unlike other frameworks, an application in Clay Framework is the acting Controller.
		<h4>Framework</h4>
		A framework is software that provides basic functionality with the intent of a developer extending it. Clay Framework is written to provide basic API and configuration access to allow
		developers to shape it in their own way. Clay Framework's name is intended to reflect the formability of the material clay.
		<h4>Instance</h4>
		An instance is a specific occurence of something that can or will happen again. A package instance is an installation of a package, which can be a site or other purpose.
		<h4>Package</h4>
		A package is an application using the Installer Application API. The primary purpose of a package is to allow developers to create installers and provide them in a central location
		to the user. Packages aren't only for creating web sites, they can also be utilities that assist in web site deployment. An example of this is the Installer package, the default
		package used by the Clay Installer.
		<h4>Site Database</h4>
		A site database in the Installer is a reference to a system database. While system databases are tracked using detailed connection information, site databases are more generic
		and are pointers to allow sites (package instances) to have their own set of databases.
		<h4>System Database</h4>
		A system database in the Installer is the connection information associated with a specific database server, user, and database. The use of system databases allows sites to share
		from the database pool and selectively use databases as needed.
		<h4>Theme</h4>
		A theme is the part of an application or web site that provides the layout. In design patterns, such as MVC, it would be considered the V - View. Although Clay's themes do not
		strictly follow the traditional View function of the MVC pattern, every design aspect of an application or web site is customizable through the themes.
</div>

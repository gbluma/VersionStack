
VersionStack
============

VersionStack is an experimental PHP Service-Oriented Architecture (SOA) that makes it possible to serve,
not just a single web service, but multiple, concurrently, and with very little friction. VersionStack 
uses process forking to serve multiple versions of the same service. If one experiences a problem and
dies, it should not cause any others to fail.


Features
--------
 * Easily serve multiple version of a service in tandem.
   - Services become immutable
	 - New changes don't break old ones.
	 - Specific versions can be specified so only the ones you want will start.

 * Versions are handled using plain Git tags.
   - Copies of the source are exported to a deployment folder before running.
	 - Each version runs in an isolated servlet.

 * Services are self-hosted PHP processes. 
   - No dynamic Apache/Nginx configuration needed.
   - Uses Unix process forking to achieve multiple deployments.

 * Services restart automatically if they die. 
   - Supervisor processes ensure that services restart properly

Why you shouldn't use it
------------------------
 * Currently, your project MUST use Git for your project. VersionStack uses Git tags to export copies of the
   source and serve committed files. 
 * Self-hosted PHP web servers are not as safe as fastcgi or Apache. Until I know more about how the 
   services run, this is experimental.
 * This is a hobby-project, bugs are likely and support is minimal. It *IS* open source, so *you* can help
   solve both of those problems, but it is not battle-hardened... Yet.
   

Dependencies
-----------
 * Git (required) - VersionStack uses git to export multiple versions of a service to disk.
 * PHP pcntl extension 
 * Unix/Linux -- needed for process forking.


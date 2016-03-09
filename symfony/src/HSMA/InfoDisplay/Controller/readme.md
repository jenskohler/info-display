# Controller classes

This directory contains the controller classes. The controllers are called when a specific URL is contacted. The mapping from URL to controller is called *routing* in Symfony and is configured by the `routing.yml` file in the `Resources/config` file.

A controller can have an arbitrary number of methods. The methods ending with `Action` are called by the framework if a route matches (the `Action` part is omitted in `routing.yml`).

The actual rendering of the results is performed by a Twig-Template found in the `Resources/views` folder.

# Form classes

This directory contains the form classes. The purpose of these classes is to create forms that can be filled with data by the user of the web application.

Each form class corresponds to an entity class (in the `Entity` folder). The form class creates a form. The data of the form is then stored in the entity class.

The name of the form field must fit to a property (getter and setter) of the entity class. E.g. the form field `query` will be stored in the property accessible via `setQuery` and `getQuery`.

It is possible to pass data from the controller to the form via the options array. Normally the options that can be passed are fixed but new options can be added via the `setDefaultOptions` method.
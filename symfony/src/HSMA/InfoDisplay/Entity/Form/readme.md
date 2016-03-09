# Entities for Forms

This directory contains the entity classes. The purpose of these classes is to store the input from a form and transport that input to the respective controller.

Each entity class corresponds to a form class (in the `Form` folder with the name suffix `Type`). The form class creates a form. The data of the form is then stored in the entity class.

The name of the form field must fit to a property (getter and setter) of the entity class. E.g. the form field `query` will be stored in the property accessible via `setQuery` and `getQuery`.

The data from the form is primarily plain text or simple data types. The entity class can offer methods to transfer this data in high level data (objects, arrays)
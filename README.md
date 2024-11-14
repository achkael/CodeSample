# InterdrinksEcommerceAPI (IEA)

The general purpose of this API is to give a possibility to interact with Interdrink's main database.
Here is the Interdrinks Ecommerce API (IEA), using Symfony's framework with it's API Component

## Code Sample from WarehouselocationProductHistory

This dataTable store all movements for each products in a specific warehouse

The goal of this code is to be able to export a large amount of data into CSV file using filters from Back-office (table size is multiple million of rows)
To do so i used the DataProvider to format data and DataTransformer to apply filters
The Export to CSV route is defined using ApiPlatform annotations on Entity\WarehouseLocationProductHistory
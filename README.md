# Sheet export addon for Cockpit CMS

**This addon is not compatible with Cockpit CMS v2.**

See also [Cockpit CMS v1 docs](https://v1.getcockpit.com/documentation), [Cockpit CMS v1 repo](https://github.com/agentejo/cockpit) and [Cockpit CMS v2 docs](https://getcockpit.com/documentation/), [Cockpit CMS v2 repo](https://github.com/Cockpit-HQ/Cockpit).

---

This is a draft. I didn't built a graphical user interface etc. and I did only a few tests. Actually, this is just a quick, modified version of the sheet export in my [Tables addon][1], where I added advanced filters and where I had control over the content types.

Replace `http://localhost:8080` in the examples with `https://your-domain.com`, where Cockpit is available.

To export as json (like the core/default), call

`http://localhost:8080/collections/export/collection_name`

To export as CSV/XLSX/ODT, add `?type=csv` to the url.

* `http://localhost:8080/collections/export/collection_name?type=csv`
* `http://localhost:8080/collections/export/collection_name?type=odt`
* `http://localhost:8080/collections/export/collection_name?type=xls`
* `http://localhost:8080/collections/export/collection_name?type=xlsx`

To use field labels instead of names as table names, call

`http://localhost:8080/collections/export/collection_name?type=odt&options[pretty]=1`


## Filter fields

remove title from output:

`http://localhost:8080/collections/export/collection_name?type=xls&options[fields][title]=0`

export only title and hide _id:

`http://localhost:8080/collections/export/collection_name?type=xls&options[fields][title]=1&options[fields][_id]=0`



## Credits and third party resources

For exporting spreadsheets, I used [PhpSpreadsheet][2], which is released under the [LGPL 2.1 License][3].



[1]: https://github.com/raffaelj/cockpit_Tables
[2]: https://github.com/PHPOffice/PhpSpreadsheet
[3]: https://github.com/PHPOffice/PhpSpreadsheet/blob/master/LICENSE

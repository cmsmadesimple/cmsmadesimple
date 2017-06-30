In this release some subtle but important changes have been introduced that you should be aware of:

a:  Page alias generation has now been improved to disallow entirely numeric page aliases.
    This is to avoid confusion where a page alias may be incorrectly identified as a numeric page id in functions like {cms_selflink} and various other API functions.
    When creating a new page, a page alias that is entirely numeric will be prefixed by a 'p' character.
    When updating pages, an error will be generated if the existing page is entirely numeric.
    Aliases such as 12345-text are not entirely numeric therefore they are permitted.

b:  Path's are no longer allowed in template resource specifications.
    i.e:  {include file='path/to/template.tpl'} will not work and will generate an error.
    This is to circumvent potential attacks.

c:  In 2.2 we introduced the concept of mact-preprocessing which processes the module action and caches its output in memory, before the {content} tag is called.
    This helps with variable scope problems, but caused other problems.  In 2.2.2 we moved the mact-preprocessing functionality to be called AFTER the top portion of the template.
    Therefore template processing now occurs in this order:

    1.  The top portion of the template, before <html>
    2.  mact-preprocessing
    3.  The body portion of the template.
    4.  The head portion of the template.

Please read the changelog for a more complete list of exactly what has been changed, fixed or improved in this release.

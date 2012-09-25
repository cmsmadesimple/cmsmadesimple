<ul>
<li>1.1 - Original release</li>
<li>1.2 - April 2007 (calguy1000)
  <p>Added the ability to limit results to certain modules,and added the ability to pass parameters through to the various modules to allow different formatting of the output.</p>
  <p>Modified so that the search module could be used in non default content blocks.</p>
</li>
<li>1.3 - May 2007 (calguy1000)
  <p>Adds calls to SetParameterType</p>
</li>
<li>1.4 - Nov 2007 (calguy1000)
  <p>Adds the keywords action.</p>
  <p>Fixes a problem with using the resultpage parameter to a page that's template was dramatically different.</p>
</li>
<li>1.4.1 - Nov 2007 (calguy1000)
  <p>Minor fixes so that php tags, and punctuation characters are not indexed.</p>
  <p>Fix the VisibleToAdminUser method</p>
</li>
<li>1.5 - Mar 2008 (calguy1000)
  <p>Now gather statistics about the top most 50 frequently searched words.</p>
  <p>Addd the ability to display the statistics and clear them from the admin panel.</p>
  <p>Make it easier to style the search button, and its label.</p>
  <p>Now only index active pages</p>
</li>
<li>1.5.1 - July 2008 (calguy1000)
  <p>Minor changes to the template to restore behaviour that was there before 1.5</p>
  <p>Now escape any search term characters that have special meanings in regular expressions</p>
  <p>Minor optimizations and tweaks</p>
</li>
<li>1.5.3 - February 2009 (calguy1000)
  <ul>
    <li>Dont return page results for non searchable pages, even if the result was found in the template.</li>
    <li>Now remove event handlers when uninstalling the module.</li>
    <li>Move changelog external.</li>
    <li>Make changes to convert search results to entities to help with validation.</li>
    <li>Now check for the search stop comment in the addwords call.</li>
  </ul>
</li>
<li>1.5.4 - March 2009 (calguy1000)
  <ul>
    <li>Add ability to track search phrases (optionally) in the statistics table.</li>
    <li>Add ability to export search phrases and words to csv.</li>
  </ul>
</li>
<li>1.6 - May 2009 (calguy1000)
  <ul>
    <li>Add some lazy loading</li>
  </ul>
</li>
<li>1.6.3 - April 2010 (calguy1000)
  <ul>
    <li>Adds some indexes.</li>
    <li>Fixes searchhilite stuff where matches are non words.</li>
  </ul>
</li>
<li>1.6.4 - May 2010 (calguy1000)
  <ul>
    <li>Minor bug fixes.</li>
  </ul>
</li>
<li>1.6.6 - July 2010 (calguy1000)
  <ul>
    <li>Minor bug fixes.</li>
  </ul>
</li>
</ul>

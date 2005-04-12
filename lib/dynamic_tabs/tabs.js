/*
Dynamic Tabs 1.0
Copyright (c) 2005 Rob Allen (rob at akrabat dot com)

Permission is hereby granted, free of charge, to any person 
obtaining a copy of this software and associated documentation 
files (the "Software"), to deal in the Software without restriction,
including without limitation the rights to use, copy, modify, merge,
publish, distribute, sublicense, and/or sell copies of the Software,
and to permit persons to whom the Software is furnished to do so,
subject to the following conditions:

The above copyright notice and this permission notice shall be 
included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, 
EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES 
OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND 
NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT 
HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY,
WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER 
DEALINGS IN THE SOFTWARE.
 
*/


function getChildElementsByClassName(parentElement, className)
{
	var i, childElements, pattern, result;
	result = new Array();
	pattern = new RegExp("\\b"+className+"\\b");


	childElements = parentElement.getElementsByTagName('*');
	for(i = 0; i < childElements.length; i++)
	{
		if(childElements[i].className.search(pattern) != -1)
		{
			result[result.length] = childElements[i];
		}
	}
	return result;
}


function BuildTabs(containerName, headerName, tablistName)
{
	var i, tabContainer, tabContents, tabHeading, title, tabElement;
	var divElement, ulElement, liElement, tabLink, linkText;
    if (containerName == null)
        {
        containerName = 'tab-container';
        }
    if (headerName == null)
        {
        headerName = 'tab-header';
        }
    if (tablistName == null)
        {
        tablistName = 'tab-list';
        }

	// assume that if document.getElementById exists, then this will work...
	if(! eval('document.getElementById') ) return;

	tabContainer = document.getElementById(containerName);
	if(tabContainer == null)
		return;

	tabContents = getChildElementsByClassName(tabContainer, 'tab-content');
	if(tabContents.length == 0)
		return;

	divElement = document.createElement("div");
	divElement.id = headerName;
	ulElement = document.createElement("ul");
	ulElement.id = tablistName;

	tabContainer.insertBefore(divElement, tabContents[0]);
	divElement.appendChild(ulElement);

	for(i = 0 ; i < tabContents.length; i++)
	{
		tabHeading = getChildElementsByClassName(tabContents[i], 'tab');
		title = tabHeading[0].childNodes[0].nodeValue;


		// create the tabs as an unsigned list
		liElement = document.createElement("li");
		tabLink = document.createElement("a");
		linkText = document.createTextNode(title);

		tabLink.className = "tab-item";

		tabLink.setAttribute("href","javascript://");
		tabLink.setAttribute( "title", tabHeading[0].getAttribute("title"));
		if (! (containerName == "tab-container"))
		  {
		  tabLink.onclick = new Function ("ActivateTab(" + i + ",'"+containerName+"','"+tablistName+"')");
		  }
        else
          {
		  tabLink.onclick = new Function ("ActivateTab(" + i + ")");
          }

		ulElement.appendChild(liElement);
		liElement.appendChild(tabLink);
		tabLink.appendChild(linkText);

		// remove the H1
		tabContents[i].removeChild
	}
}

function ActivateTab(activeTabIndex, containerName, tablistName)
{
	var i, tabContainer, tabContents, tabList, tabs;
    if (containerName == null)
        {
        containerName = 'tab-container';
        }
    if (tablistName == null)
        {
        tablistName = 'tab-list';
        }


	tabContainer = document.getElementById(containerName);
	if(tabContainer == null)
		return;

	tabContents = getChildElementsByClassName(tabContainer, 'tab-content');
	if(tabContents.length > 0)
	{
		for(i = 0; i < tabContents.length; i++)
		{
			tabContents[i].style.display = "none";
		}

		tabContents[activeTabIndex].style.display = "block";


		tabList = document.getElementById(tablistName);
		tabs = getChildElementsByClassName(tabContainer, 'tab-item');
		if(tabs.length > 0)
		{
			for(i = 0; i < tabs.length; i++)
			{
				tabs[i].className = "tab-item";
			}

			tabs[activeTabIndex].className = "tab-item tab-active";
			tabs[activeTabIndex].blur();
		}
	}
}
BuildTabs('tab-container','tab-header','tab-list');
ActivateTab(0,'tab-container','tab-list');

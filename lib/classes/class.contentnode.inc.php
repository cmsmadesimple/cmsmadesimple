<!DOCTYPE html
    PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en">
<head>

 <title>/trunk/lib/classes/class.contentnode.inc.php - CMS Made Simple - Trac</title><link rel="start" href="/cgi-bin/trac.cgi/wiki" /><link rel="search" href="/cgi-bin/trac.cgi/search" /><link rel="help" href="/cgi-bin/trac.cgi/wiki/TracGuide" /><link rel="stylesheet" href="/cgi-bin/trac.cgi/chrome/common/css/trac.css" type="text/css" /><link rel="stylesheet" href="/cgi-bin/trac.cgi/chrome/common/css/code.css" type="text/css" /><link rel="stylesheet" href="/cgi-bin/trac.cgi/chrome/common/css/browser.css" type="text/css" /><link rel="icon" href="/cgi-bin/trac.cgi/chrome/common/trac.ico" type="image/x-icon" /><link rel="shortcut icon" href="/cgi-bin/trac.cgi/chrome/common/trac.ico" type="image/x-icon" /><link rel="up" href="/cgi-bin/trac.cgi/browser/trunk/lib/classes?rev=2332" title="Parent directory" /><link rel="alternate" href="/cgi-bin/trac.cgi/browser/trunk/lib/classes/class.contentnode.inc.php?rev=2332&amp;format=txt" title="Plain Text" type="text/plain" /><link rel="alternate" href="/cgi-bin/trac.cgi/browser/trunk/lib/classes/class.contentnode.inc.php?rev=2332&amp;format=raw" title="Original Format" type="text/x-php" /><style type="text/css">
</style>
 <script type="text/javascript" src="/cgi-bin/trac.cgi/chrome/common/js/trac.js"></script>
   <link rel="stylesheet" type="text/css" href="http://cmsmadesimple.org/stylesheet.php?templateid=6" />
</head>
<body>



<div id="page">

   <div id="page_top">
      <div id="header">
         <div id="header_left"><img src="http://cmsmadesimple.org/uploads/images/logo.gif" width="175" height="100"/></div>
         <div id="header-right">
            <div id="top-nav">
         
        <ul class="menu_horiz" id="topmenu">
<li><a href="http://cmsmadesimple.org/home.shtml">Main</a></li>
<li><a href="http://cmsmadesimple.org/news.shtml">News</a></li>
<li><a href="http://cmsmadesimple.org/downloads.shtml">Downloads</a></li>
<li><a href="http://cmsmadesimple.org/Documentation.shtml">Documentation</a></li>
<li><a href="http://forum.cmsmadesimple.org/">Forum</a></li>
<li class="active"><a class="active" href="http://cmsmadesimple.org/Community.shtml">Community</a></li></ul>

             </div>
         </div>
      </div>
   </div>

   <div id="page_middle">
         <div id="main">

<h1 style="margin-bottom: 0; ">Report Bug or Feature Request</h1>






<div id="mainnav"><ul><li class="first"><a href="/cgi-bin/trac.cgi/wiki" accesskey="1">Wiki</a></li><li><a href="/cgi-bin/trac.cgi/timeline" accesskey="2">Timeline</a></li><li><a href="/cgi-bin/trac.cgi/roadmap" accesskey="3">Roadmap</a></li><li class="active"><a href="/cgi-bin/trac.cgi/browser">Browse Source</a></li><li><a href="/cgi-bin/trac.cgi/report">View Tickets</a></li><li><a href="/cgi-bin/trac.cgi/newticket" accesskey="7">New Ticket</a></li><li class="last"><a href="/cgi-bin/trac.cgi/search" accesskey="4">Search</a></li></ul></div>


<div style="padding: 0;	padding-bottom: 10px; float: left; margin-left: 15px;" class="nav">
 <h2>Report Navigation</h2>
 
  
  
</div>




<div id="metanav" class="nav"><ul><li class="first"><a href="/cgi-bin/trac.cgi/login">Login</a></li><li><a href="/cgi-bin/trac.cgi/settings">Settings</a></li><li><a href="/cgi-bin/trac.cgi/wiki/TracGuide" accesskey="6">Help/Guide</a></li><li class="last"><a href="/cgi-bin/trac.cgi/about" accesskey="9">About Trac</a></li></ul>
</div>


<!--form style="margin-right: 15px;" id="search" action="/cgi-bin/trac.cgi/search" method="get">
 <div>
  <label for="proj-search">Search:</label>
  <input type="text" id="proj-search" name="q" size="10" accesskey="f" value="" />
  <input type="submit" value="Search" />
  <input type="hidden" name="wiki" value="on" />
  <input type="hidden" name="changeset" value="on" />
  <input type="hidden" name="ticket" value="on" />
 </div>
</form-->




<div id="ctxtnav" class="nav">
 <ul>
  <li class="last"><a href="/cgi-bin/trac.cgi/log/trunk/lib/classes/class.contentnode.inc.php">Revision Log</a></li>
 </ul>
</div>

<div id="content-trac" class="browser">
 <h2><a class="first" title="Go to root directory" href="/cgi-bin/trac.cgi/browser/?rev=2332">root</a><span class="sep">/</span><a title="View trunk" href="/cgi-bin/trac.cgi/browser/trunk?rev=2332">trunk</a><span class="sep">/</span><a title="View lib" href="/cgi-bin/trac.cgi/browser/trunk/lib?rev=2332">lib</a><span class="sep">/</span><a title="View classes" href="/cgi-bin/trac.cgi/browser/trunk/lib/classes?rev=2332">classes</a><span class="sep">/</span><a title="View class.contentnode.inc.php" href="/cgi-bin/trac.cgi/browser/trunk/lib/classes/class.contentnode.inc.php?rev=2332">class.contentnode.inc.php</a></h2>

 <div id="jumprev">
  <form action="" method="get"><div>
   <label for="rev">View revision:</label>
   <input type="text" id="rev" name="rev" value="2332" size="4" />
  </div></form>
 </div>

 
  <table id="info" summary="Revision info"><tr>
    <th scope="row">
     Revision <a href="/cgi-bin/trac.cgi/changeset/2332">2332</a>
     (checked in by melix, 2 hours ago)
    </th>
    <td class="message"><p>
Fixed hierarchy manager so that it works on both PHP 4 and PHP 5 <br />
Changed the return type of <a class="missing wiki" href="/cgi-bin/trac.cgi/wiki/GetAllContentAsHierarchy" rel="nofollow">GetAllContentAsHierarchy?</a> <br />
</p>
</td>
   </tr></tr>
  </table>
  <div id="preview"><table class="code"><thead><tr><th class="lineno">Line</th><th class="content">&nbsp;</th></tr></thead><tbody><tr><th id="L1"><a href="#L1">1</a></th>
<td><span class="code-lang">&lt;?php</span></td>
</tr><tr><th id="L2"><a href="#L2">2</a></th>
<td><span class="code-lang"></span><span class="code-comment">#CMS - CMS Made Simple</span></td>
</tr><tr><th id="L3"><a href="#L3">3</a></th>
<td><span class="code-comment">#(c)2004 by Ted Kulp (wishy@users.sf.net)</span></td>
</tr><tr><th id="L4"><a href="#L4">4</a></th>
<td><span class="code-comment">#This project's homepage is: http://cmsmadesimple.sf.net</span></td>
</tr><tr><th id="L5"><a href="#L5">5</a></th>
<td><span class="code-comment">#</span></td>
</tr><tr><th id="L6"><a href="#L6">6</a></th>
<td><span class="code-comment">#This program is free software; you can redistribute it and/or modify</span></td>
</tr><tr><th id="L7"><a href="#L7">7</a></th>
<td><span class="code-comment">#it under the terms of the GNU General Public License as published by</span></td>
</tr><tr><th id="L8"><a href="#L8">8</a></th>
<td><span class="code-comment">#the Free Software Foundation; either version 2 of the License, or</span></td>
</tr><tr><th id="L9"><a href="#L9">9</a></th>
<td><span class="code-comment">#(at your option) any later version.</span></td>
</tr><tr><th id="L10"><a href="#L10">10</a></th>
<td><span class="code-comment">#</span></td>
</tr><tr><th id="L11"><a href="#L11">11</a></th>
<td><span class="code-comment">#This program is distributed in the hope that it will be useful,</span></td>
</tr><tr><th id="L12"><a href="#L12">12</a></th>
<td><span class="code-comment">#but WITHOUT ANY WARRANTY; without even the implied warranty of</span></td>
</tr><tr><th id="L13"><a href="#L13">13</a></th>
<td><span class="code-comment">#MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.&nbsp; See the</span></td>
</tr><tr><th id="L14"><a href="#L14">14</a></th>
<td><span class="code-comment">#GNU General Public License for more details.</span></td>
</tr><tr><th id="L15"><a href="#L15">15</a></th>
<td><span class="code-comment">#You should have received a copy of the GNU General Public License</span></td>
</tr><tr><th id="L16"><a href="#L16">16</a></th>
<td><span class="code-comment">#along with this program; if not, write to the Free Software</span></td>
</tr><tr><th id="L17"><a href="#L17">17</a></th>
<td><span class="code-comment">#Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA&nbsp; 02111-1307&nbsp; USA</span></td>
</tr><tr><th id="L18"><a href="#L18">18</a></th>
<td><span class="code-comment">#</span></td>
</tr><tr><th id="L19"><a href="#L19">19</a></th>
<td><span class="code-comment"></span></td>
</tr><tr><th id="L20"><a href="#L20">20</a></th>
<td><span class="code-comment"></span><span class="code-keyword">class </span><span class="code-lang">ContentNode </span><span class="code-keyword">{</span></td>
</tr><tr><th id="L21"><a href="#L21">21</a></th>
<td><span class="code-keyword">&nbsp; var </span><span class="code-lang">$content</span><span class="code-keyword">;</span></td>
</tr><tr><th id="L22"><a href="#L22">22</a></th>
<td><span class="code-keyword">&nbsp; var </span><span class="code-lang">$parentNode</span><span class="code-keyword">;</span></td>
</tr><tr><th id="L23"><a href="#L23">23</a></th>
<td><span class="code-keyword">&nbsp; var </span><span class="code-lang">$children</span><span class="code-keyword">;</span></td>
</tr><tr><th id="L24"><a href="#L24">24</a></th>
<td><span class="code-keyword">&nbsp; var </span><span class="code-lang">$level</span><span class="code-keyword">;</span></td>
</tr><tr><th id="L25"><a href="#L25">25</a></th>
<td><span class="code-keyword"></span></td>
</tr><tr><th id="L26"><a href="#L26">26</a></th>
<td><span class="code-keyword">&nbsp; function </span><span class="code-lang">ContentNode</span><span class="code-keyword">() {&nbsp; </span></td>
</tr><tr><th id="L27"><a href="#L27">27</a></th>
<td><span class="code-keyword">&nbsp;&nbsp; &nbsp;</span><span class="code-lang">$this</span><span class="code-keyword">-&gt;</span><span class="code-lang">children </span><span class="code-keyword">= array();</span></td>
</tr><tr><th id="L28"><a href="#L28">28</a></th>
<td><span class="code-keyword">&nbsp;&nbsp; &nbsp;</span><span class="code-lang">$this</span><span class="code-keyword">-&gt;</span><span class="code-lang">level</span><span class="code-keyword">=</span><span class="code-lang">0</span><span class="code-keyword">;</span></td>
</tr><tr><th id="L29"><a href="#L29">29</a></th>
<td><span class="code-keyword">&nbsp;&nbsp; &nbsp;</span><span class="code-lang">$this</span><span class="code-keyword">-&gt;</span><span class="code-lang">parentNode</span><span class="code-keyword">=</span><span class="code-lang">null</span><span class="code-keyword">;</span></td>
</tr><tr><th id="L30"><a href="#L30">30</a></th>
<td><span class="code-keyword">&nbsp;&nbsp; &nbsp;</span><span class="code-lang">$this</span><span class="code-keyword">-&gt;</span><span class="code-lang">content</span><span class="code-keyword">=</span><span class="code-lang">null</span><span class="code-keyword">;</span></td>
</tr><tr><th id="L31"><a href="#L31">31</a></th>
<td><span class="code-keyword">&nbsp; }</span></td>
</tr><tr><th id="L32"><a href="#L32">32</a></th>
<td><span class="code-keyword"></span></td>
</tr><tr><th id="L33"><a href="#L33">33</a></th>
<td><span class="code-keyword">&nbsp; function </span><span class="code-lang">init</span><span class="code-keyword">(&amp;</span><span class="code-lang">$content</span><span class="code-keyword">, &amp;</span><span class="code-lang">$parentNode</span><span class="code-keyword">) {</span></td>
</tr><tr><th id="L34"><a href="#L34">34</a></th>
<td><span class="code-keyword">&nbsp;&nbsp; &nbsp;</span><span class="code-lang">$this</span><span class="code-keyword">-&gt;</span><span class="code-lang">content </span><span class="code-keyword">= &amp;</span><span class="code-lang">$content</span><span class="code-keyword">;</span></td>
</tr><tr><th id="L35"><a href="#L35">35</a></th>
<td><span class="code-keyword">&nbsp;&nbsp; &nbsp;</span><span class="code-lang">$this</span><span class="code-keyword">-&gt;</span><span class="code-lang">parentNode </span><span class="code-keyword">= &amp;</span><span class="code-lang">$parentNode</span><span class="code-keyword">;</span></td>
</tr><tr><th id="L36"><a href="#L36">36</a></th>
<td><span class="code-keyword">&nbsp;&nbsp; &nbsp;if (isset(</span><span class="code-lang">$parentNode</span><span class="code-keyword">)) {</span></td>
</tr><tr><th id="L37"><a href="#L37">37</a></th>
<td><span class="code-keyword">&nbsp;&nbsp; &nbsp; &nbsp;</span><span class="code-lang">$this</span><span class="code-keyword">-&gt;</span><span class="code-lang">level</span><span class="code-keyword">=</span><span class="code-lang">$parentNode</span><span class="code-keyword">-&gt;</span><span class="code-lang">getLevel</span><span class="code-keyword">()+</span><span class="code-lang">1</span><span class="code-keyword">;</span></td>
</tr><tr><th id="L38"><a href="#L38">38</a></th>
<td><span class="code-keyword">&nbsp;&nbsp; &nbsp;}</span></td>
</tr><tr><th id="L39"><a href="#L39">39</a></th>
<td><span class="code-keyword">&nbsp; }</span></td>
</tr><tr><th id="L40"><a href="#L40">40</a></th>
<td><span class="code-keyword">&nbsp; </span></td>
</tr><tr><th id="L41"><a href="#L41">41</a></th>
<td><span class="code-keyword">&nbsp; function </span><span class="code-lang">setParentNode</span><span class="code-keyword">(&amp;</span><span class="code-lang">$node</span><span class="code-keyword">) {</span></td>
</tr><tr><th id="L42"><a href="#L42">42</a></th>
<td><span class="code-keyword">&nbsp;&nbsp; &nbsp;</span><span class="code-lang">$this</span><span class="code-keyword">-&gt;</span><span class="code-lang">parentNode </span><span class="code-keyword">= &amp;</span><span class="code-lang">$node</span><span class="code-keyword">;</span></td>
</tr><tr><th id="L43"><a href="#L43">43</a></th>
<td><span class="code-keyword">&nbsp;&nbsp; &nbsp;if (isset(</span><span class="code-lang">$node</span><span class="code-keyword">)) {</span></td>
</tr><tr><th id="L44"><a href="#L44">44</a></th>
<td><span class="code-keyword">&nbsp;&nbsp; &nbsp; &nbsp;</span><span class="code-lang">$this</span><span class="code-keyword">-&gt;</span><span class="code-lang">level</span><span class="code-keyword">=</span><span class="code-lang">$node</span><span class="code-keyword">-&gt;</span><span class="code-lang">getLevel</span><span class="code-keyword">()+</span><span class="code-lang">1</span><span class="code-keyword">;</span></td>
</tr><tr><th id="L45"><a href="#L45">45</a></th>
<td><span class="code-keyword">&nbsp;&nbsp; &nbsp;}</span></td>
</tr><tr><th id="L46"><a href="#L46">46</a></th>
<td><span class="code-keyword">&nbsp; }</span></td>
</tr><tr><th id="L47"><a href="#L47">47</a></th>
<td><span class="code-keyword">&nbsp; </span></td>
</tr><tr><th id="L48"><a href="#L48">48</a></th>
<td><span class="code-keyword">&nbsp; function </span><span class="code-lang">setContent</span><span class="code-keyword">(&amp;</span><span class="code-lang">$content</span><span class="code-keyword">) {</span></td>
</tr><tr><th id="L49"><a href="#L49">49</a></th>
<td><span class="code-keyword">&nbsp;&nbsp; &nbsp;</span><span class="code-lang">$this</span><span class="code-keyword">-&gt;</span><span class="code-lang">content </span><span class="code-keyword">= &amp;</span><span class="code-lang">$content</span><span class="code-keyword">;</span></td>
</tr><tr><th id="L50"><a href="#L50">50</a></th>
<td><span class="code-keyword">&nbsp; }</span></td>
</tr><tr><th id="L51"><a href="#L51">51</a></th>
<td><span class="code-keyword">&nbsp; </span></td>
</tr><tr><th id="L52"><a href="#L52">52</a></th>
<td><span class="code-keyword">&nbsp; function </span><span class="code-lang">getChildrenCount</span><span class="code-keyword">() {</span></td>
</tr><tr><th id="L53"><a href="#L53">53</a></th>
<td><span class="code-keyword">&nbsp;&nbsp; &nbsp;return </span><span class="code-lang">count</span><span class="code-keyword">(</span><span class="code-lang">$this</span><span class="code-keyword">-&gt;</span><span class="code-lang">children</span><span class="code-keyword">);</span></td>
</tr><tr><th id="L54"><a href="#L54">54</a></th>
<td><span class="code-keyword">&nbsp; }</span></td>
</tr><tr><th id="L55"><a href="#L55">55</a></th>
<td><span class="code-keyword">&nbsp; </span></td>
</tr><tr><th id="L56"><a href="#L56">56</a></th>
<td><span class="code-keyword">&nbsp; function &amp;</span><span class="code-lang">getContent</span><span class="code-keyword">() {</span></td>
</tr><tr><th id="L57"><a href="#L57">57</a></th>
<td><span class="code-keyword">&nbsp;&nbsp; &nbsp;return </span><span class="code-lang">$this</span><span class="code-keyword">-&gt;</span><span class="code-lang">content</span><span class="code-keyword">;</span></td>
</tr><tr><th id="L58"><a href="#L58">58</a></th>
<td><span class="code-keyword">&nbsp; }</span></td>
</tr><tr><th id="L59"><a href="#L59">59</a></th>
<td><span class="code-keyword">&nbsp; </span></td>
</tr><tr><th id="L60"><a href="#L60">60</a></th>
<td><span class="code-keyword">&nbsp; function &amp;</span><span class="code-lang">getParentNode</span><span class="code-keyword">() {</span></td>
</tr><tr><th id="L61"><a href="#L61">61</a></th>
<td><span class="code-keyword">&nbsp;&nbsp; &nbsp;return </span><span class="code-lang">$this</span><span class="code-keyword">-&gt;</span><span class="code-lang">parentNode</span><span class="code-keyword">;</span></td>
</tr><tr><th id="L62"><a href="#L62">62</a></th>
<td><span class="code-keyword">&nbsp; }</span></td>
</tr><tr><th id="L63"><a href="#L63">63</a></th>
<td><span class="code-keyword">&nbsp; </span></td>
</tr><tr><th id="L64"><a href="#L64">64</a></th>
<td><span class="code-keyword">&nbsp; function &amp;</span><span class="code-lang">getChildren</span><span class="code-keyword">() {</span></td>
</tr><tr><th id="L65"><a href="#L65">65</a></th>
<td><span class="code-keyword">&nbsp;&nbsp; &nbsp;return </span><span class="code-lang">$this</span><span class="code-keyword">-&gt;</span><span class="code-lang">children</span><span class="code-keyword">;</span></td>
</tr><tr><th id="L66"><a href="#L66">66</a></th>
<td><span class="code-keyword">&nbsp; }</span></td>
</tr><tr><th id="L67"><a href="#L67">67</a></th>
<td><span class="code-keyword">&nbsp; </span></td>
</tr><tr><th id="L68"><a href="#L68">68</a></th>
<td><span class="code-keyword">&nbsp; function </span><span class="code-lang">hasChildren</span><span class="code-keyword">() {</span></td>
</tr><tr><th id="L69"><a href="#L69">69</a></th>
<td><span class="code-keyword">&nbsp;&nbsp; &nbsp;return (</span><span class="code-lang">count</span><span class="code-keyword">(</span><span class="code-lang">$this</span><span class="code-keyword">-&gt;</span><span class="code-lang">children</span><span class="code-keyword">)&gt;</span><span class="code-lang">0</span><span class="code-keyword">);</span></td>
</tr><tr><th id="L70"><a href="#L70">70</a></th>
<td><span class="code-keyword">&nbsp; }</span></td>
</tr><tr><th id="L71"><a href="#L71">71</a></th>
<td><span class="code-keyword">&nbsp; </span></td>
</tr><tr><th id="L72"><a href="#L72">72</a></th>
<td><span class="code-keyword">&nbsp; function </span><span class="code-lang">getLevel</span><span class="code-keyword">() {</span></td>
</tr><tr><th id="L73"><a href="#L73">73</a></th>
<td><span class="code-keyword">&nbsp;&nbsp; &nbsp;return </span><span class="code-lang">$this</span><span class="code-keyword">-&gt;</span><span class="code-lang">level</span><span class="code-keyword">;</span></td>
</tr><tr><th id="L74"><a href="#L74">74</a></th>
<td><span class="code-keyword">&nbsp; }</span></td>
</tr><tr><th id="L75"><a href="#L75">75</a></th>
<td><span class="code-keyword"></span></td>
</tr><tr><th id="L76"><a href="#L76">76</a></th>
<td><span class="code-keyword">&nbsp; function </span><span class="code-lang">addChild</span><span class="code-keyword">(&amp;</span><span class="code-lang">$node</span><span class="code-keyword">) {</span></td>
</tr><tr><th id="L77"><a href="#L77">77</a></th>
<td><span class="code-keyword">&nbsp;&nbsp; &nbsp;</span><span class="code-lang">$content </span><span class="code-keyword">= &amp;</span><span class="code-lang">$node</span><span class="code-keyword">-&gt;</span><span class="code-lang">getContent</span><span class="code-keyword">();</span></td>
</tr><tr><th id="L78"><a href="#L78">78</a></th>
<td><span class="code-keyword">&nbsp;&nbsp; &nbsp;</span><span class="code-comment">//echo "Adding ".$content-&gt;Hierarchy()." to level $this-&gt;level&lt;br/&gt;";</span></td>
</tr><tr><th id="L79"><a href="#L79">79</a></th>
<td><span class="code-comment">&nbsp;&nbsp; &nbsp;</span><span class="code-lang">$this</span><span class="code-keyword">-&gt;</span><span class="code-lang">children</span><span class="code-keyword">[] = &amp;</span><span class="code-lang">$node</span><span class="code-keyword">;</span></td>
</tr><tr><th id="L80"><a href="#L80">80</a></th>
<td><span class="code-keyword">&nbsp;&nbsp; &nbsp;</span><span class="code-comment">//echo "Total nodes of level $this-&gt;level = ".count($this-&gt;children)."&lt;br/&gt;";</span></td>
</tr><tr><th id="L81"><a href="#L81">81</a></th>
<td><span class="code-comment">&nbsp; </span><span class="code-keyword">}</span></td>
</tr><tr><th id="L82"><a href="#L82">82</a></th>
<td><span class="code-keyword">&nbsp; </span></td>
</tr><tr><th id="L83"><a href="#L83">83</a></th>
<td><span class="code-keyword">&nbsp; </span><span class="code-comment">/**</span></td>
</tr><tr><th id="L84"><a href="#L84">84</a></th>
<td><span class="code-comment">&nbsp;&nbsp; * Returns the position of a node into the list of children</span></td>
</tr><tr><th id="L85"><a href="#L85">85</a></th>
<td><span class="code-comment">&nbsp;&nbsp; * This method is a workaround for a PHP4 bug where reference testing</span></td>
</tr><tr><th id="L86"><a href="#L86">86</a></th>
<td><span class="code-comment">&nbsp;&nbsp; * returns a circular reference fatal error</span></td>
</tr><tr><th id="L87"><a href="#L87">87</a></th>
<td><span class="code-comment">&nbsp;&nbsp; * @param $node the node to find into the list of children</span></td>
</tr><tr><th id="L88"><a href="#L88">88</a></th>
<td><span class="code-comment">&nbsp;&nbsp; */</span></td>
</tr><tr><th id="L89"><a href="#L89">89</a></th>
<td><span class="code-comment">&nbsp; </span><span class="code-keyword">function </span><span class="code-lang">findChildNodeIndex</span><span class="code-keyword">(&amp;</span><span class="code-lang">$node</span><span class="code-keyword">) {</span></td>
</tr><tr><th id="L90"><a href="#L90">90</a></th>
<td><span class="code-keyword">&nbsp;&nbsp; &nbsp;</span><span class="code-lang">$i</span><span class="code-keyword">=</span><span class="code-lang">0</span><span class="code-keyword">;</span></td>
</tr><tr><th id="L91"><a href="#L91">91</a></th>
<td><span class="code-keyword">&nbsp;&nbsp; &nbsp;foreach (</span><span class="code-lang">$this</span><span class="code-keyword">-&gt;</span><span class="code-lang">children </span><span class="code-keyword">as </span><span class="code-lang">$child</span><span class="code-keyword">) {</span></td>
</tr><tr><th id="L92"><a href="#L92">92</a></th>
<td><span class="code-keyword">&nbsp;&nbsp; &nbsp; &nbsp;if (</span><span class="code-lang">$child</span><span class="code-keyword">-&gt;</span><span class="code-lang">getContent</span><span class="code-keyword">()==</span><span class="code-lang">$node</span><span class="code-keyword">-&gt;</span><span class="code-lang">getContent</span><span class="code-keyword">()) return </span><span class="code-lang">$i</span><span class="code-keyword">;</span></td>
</tr><tr><th id="L93"><a href="#L93">93</a></th>
<td><span class="code-keyword">&nbsp;&nbsp; &nbsp; &nbsp;</span><span class="code-lang">$i</span><span class="code-keyword">++;</span></td>
</tr><tr><th id="L94"><a href="#L94">94</a></th>
<td><span class="code-keyword">&nbsp;&nbsp; &nbsp;}</span></td>
</tr><tr><th id="L95"><a href="#L95">95</a></th>
<td><span class="code-keyword">&nbsp;&nbsp; &nbsp;return -</span><span class="code-lang">1</span><span class="code-keyword">;</span></td>
</tr><tr><th id="L96"><a href="#L96">96</a></th>
<td><span class="code-keyword">&nbsp; }</span></td>
</tr><tr><th id="L97"><a href="#L97">97</a></th>
<td><span class="code-keyword">&nbsp; </span></td>
</tr><tr><th id="L98"><a href="#L98">98</a></th>
<td><span class="code-keyword">}</span></td>
</tr><tr><th id="L99"><a href="#L99">99</a></th>
<td><span class="code-keyword"></span><span class="code-lang">?&gt;</span></td>
</tr><tr><th id="L100"><a href="#L100">100</a></th>
<td><span class="code-lang"></span></span></td>
</tr></tbody></table>
  </div>

 <div id="help">
  <strong>Note:</strong> See <a href="/cgi-bin/trac.cgi/wiki/TracBrowser">TracBrowser</a> for help on using the browser.
 </div>

</div>
<script type="text/javascript">searchHighlight()</script>
<div id="altlinks"><h3>Download in other formats:</h3><ul><li class="first"><a href="/cgi-bin/trac.cgi/browser/trunk/lib/classes/class.contentnode.inc.php?rev=2332&amp;format=txt">Plain Text</a></li><li class="last"><a href="/cgi-bin/trac.cgi/browser/trunk/lib/classes/class.contentnode.inc.php?rev=2332&amp;format=raw">Original Format</a></li></ul></div>

        </div>
   </div>
   
   <div id="page_bottom">
 <div id="adsense">

<!-- Begin Google AdSense Ad -->

<script type="text/javascript"><!--
google_ad_client = "pub-7958561174011032";
google_ad_width = "728";
google_ad_height = "90";
google_ad_format = "728x90_as";
google_ad_channel = "";
//--></script>
<script type="text/javascript" src="http://pagead2.googlesyndication.com/pagead/show_ads.js"></script>

<!-- End Google AdSense Ad -->

</div>

      <div id="footer"><p>CMS made simple is Free software under the GNU/GPL licence. This site is currently running CMS Made Simple 0.10.<br/>Website designed by <a href="http://www.bluebinary.com" target="_blank">bluebinary</a> and <a href="http://www.wproductions.se" target="_blank">Daniel Westergren</a> (<a href="http://www.wproductions.se" target="_blank">westis</a>)</p></div>
      
   </div>

</div>
 </body>
</html>


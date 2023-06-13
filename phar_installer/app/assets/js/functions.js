function add_message(str) {
    var theDiv = document.getElementById("inner");
    var newNode = document.createElement('p');
    newNode.className = 'message blue';
    newNode.innerHTML = str;
    theDiv.appendChild(newNode);
    theDiv.scrollTop = theDiv.scrollHeight;
}

function add_verbose(str) {
    var theDiv = document.getElementById("inner");
    var newNode = document.createElement('p');
    newNode.className = 'verbose';
    newNode.innerHTML = str;
    theDiv.appendChild(newNode);
    theDiv.scrollTop = theDiv.scrollHeight;
}

function add_error(str) {
    var theDiv = document.getElementById("inner");
    var newNode = document.createElement('p');
    newNode.innerHTML = str;
    newNode.className = 'message red';
    theDiv.appendChild(newNode);
    theDiv.scrollTop = theDiv.scrollHeight;
}

function set_block_html(id, html) {
    var theDiv = document.getElementById(id);
    theDiv.innerHTML = html;
}

function finish() {
    var theDiv = document.getElementById("bottom_nav");
    theDiv.style.display = 'block';
}

function socialShare() {

    // Twitter
    if (document.getElementById('twitter')) {
        document.getElementById('twitter').onclick = function() {
            window.open('https://twitter.com/intent/tweet?button_hashtag=cmsms&text=' + cmsms_lang.message, 'sharertwt', 'toolbar=0,status=0,width=540,height=345');
        };
    }
    // Google+
    if (document.getElementById('google')) {
        document.getElementById('google').onclick = function() {
            window.open('https://plus.google.com/share?url=http://www.cmsmadesimple.org', 'sharergplus', 'toolbar=0,status=0,width=524,height=505');
        };
    }
    // Facebook
    if (document.getElementById('facebook')) {
        document.getElementById('facebook').onclick = function() {
            window.open('http://www.facebook.com/sharer.php?u=http://www.cmsmadesimple.org', 'sharerfacebook', 'toolbar=0,status=0,width=525,height=368');
        };
    }
    // Linkedin
    if (document.getElementById('linkedin')) {
        document.getElementById('linkedin').onclick = function() {
            window.open('https://www.linkedin.com/cws/share?url=http%3A%2F%2Fwww.cmsmadesimple.org%2F&isFramed=true', 'sharerlinkedin', 'toolbar=0,status=0,width=540,height=528');
        };
    }

}

window.onload = function() {
    var freshen = document.getElementById('freshen'),
        upgrade = document.getElementById('upgrade');

    if( freshen ) {
        freshen.onclick = function() {
            return confirm(cmsms_lang.freshen);
        };
    }
    if( upgrade ) {
        upgrade.onclick = function() {
            return confirm(cmsms_lang.upgrade);
        };
    }

    socialShare();
};

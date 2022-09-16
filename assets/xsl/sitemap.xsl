<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="2.0" xmlns:html="http://www.w3.org/TR/REC-html40" xmlns:image="http://www.google.com/schemas/sitemap-image/1.1" xmlns:sitemap="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
    <xsl:output method="html" encoding="UTF-8" indent="yes"/>

    <!--
      Set variables for whether lastmod, changefreq or priority occur for any url in the sitemap.
      We do this up front because it can be expensive in a large sitemap.
      -->
    <xsl:variable name="has-lastmod" select="count( /sitemap:urlset/sitemap:url/sitemap:lastmod )"/>
    <xsl:variable name="has-changefreq" select="count( /sitemap:urlset/sitemap:url/sitemap:changefreq )"/>
    <xsl:variable name="has-image" select="count( /sitemap:urlset/sitemap:url/image:image )"/>
    <xsl:template match="/">
    <html lang="en-US">
        <head>
            <title>XML Sitemap</title>
            <style>
                /*# sourceURL=/custom.css*/
body {
    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen-Sans, Ubuntu, Cantarell, "Helvetica Neue", sans-serif;
    color: #444;
    padding: 0;
    margin: 0;
    font-size: 16px;
    box-sizing: border-box;
}
body * {
    box-sizing: border-box;
}
a:hover {
    text-decoration: underline;
}

#sitemap {
    margin: 0;
    text-align: left;
    padding: 0 .75rem;
}

#sitemap-header h1 {
    text-transform: uppercase;
    letter-spacing: 1px;
    font-weight: lighter;
    margin-bottom: 1em;
}

#sitemap-header {
    color: #fff;
    background: #2c3e50;
    margin-left: -.75rem;
    margin-right: -.75rem;
    padding: .75rem 1.5rem;
    border-bottom: 3px solid;
    padding-bottom: 4rem;
    line-height: 1.3em;
    text-align: center;
}

#sitemap-header a {
    color: #fff;
    text-decoration:none;
}

.sitemap-header-link {
    display: block;
    margin: 1rem 0 .5rem;
}

#sitemap-content {
    margin-left: -.75rem;
    margin-right: -.75rem;
    position: relative;
}

.number-sitemap-count {
    width: 6rem;
    height: 6rem;
    font-size: 1.8rem;
    text-align: center;
    background: #2c3e50;
    color: #fff;
    display: block;
    margin: -3.2rem auto 1rem;
    border-radius: 50%;
    line-height: 5.4rem;
    border: 3px solid;
}

#sitemap-content {
    direction: ltr;
    margin-left: -.75rem;
    margin-right: -.75rem;
    position: relative;
}

ul {
    list-style: none;
    padding: 20px 0;
    display: flex;
    flex-direction: column;
    flex-wrap: nowrap;
    align-content: stretch;
    align-items: stretch;
    margin: 0;
}

ul li a {
    color: #444;
    text-decoration:none;
}
ul li a:hover {
    color: #111;
}
ul li {
    font-size: .9em;
    flex: 1;
    padding: 1rem;
    display: flex;
    flex-direction: row;
    flex-wrap: nowrap;
    justify-content: space-around;
    align-items: center;
    align-content: stretch;
    width: 100%;
}
ul li:nth-child(odd) {
     background: #f1f1f1;
}
ul li span {
    flex: 1 1 calc(100% - 200px - 50px);
    width: calc(100% - 200px - 50px);
}

ul li span.lastmod {
    flex: 1 1 200px;
    text-align: center;
}

ul li span.image {
    flex: 1 1 50px;
    width: 50px;
    text-align: center;
}

ul li span.desc {
    font-weight: bold;
    font-size: .8em;
    text-align: left;
}
ul li span.lastmod,
ul li span.desc.lastmod {
    text-align: right;
    padding: 0 10px;
}
ul li span.image:not(.desc) {
    background: #42a18f;
    color: #fff;
    display: inline-block;
    border-radius: 3px;
}
            </style>
        </head>
        <body>
            <div id="sitemap">
                <div id="sitemap-header">
                    <h1 class="sitemap-header-title">XML SITEMAP</h1>
                    <div class="sitemap-header-link">
                        <a target="_blank" rel="noopener noreferrer external" href="https://www.sitemaps.org/">Learn more about XML sitemaps.</a>
                    </div>
                </div>
                <div id="sitemap-content">
                    <div class="number-sitemap-count"><xsl:value-of select="count( sitemap:urlset/sitemap:url )"/></div>
                    <xsl:if test="count( sitemap:urlset/sitemap:url ) &gt; 0">
                        <ul id="list-style-of-sitemap">
                            <li>
                                <span class="loc desc">
                                    URL
                                </span>
                                <xsl:if test="$has-lastmod">
                                    <span class="lastmod desc">
                                        LAST MODIFICATION
                                    </span>
                                </xsl:if>
                                <xsl:if test="$has-changefreq">
                                    <span class="changefreq desc">
                                        CHANGE FREQUENCY
                                    </span>
                                </xsl:if>
                                <xsl:if test="$has-image">
                                    <span class="image desc">
                                        IMAGES
                                    </span>
                                </xsl:if>
                            </li>
                            <xsl:for-each select="sitemap:urlset/sitemap:url">
                                <li>
                                    <span class="loc">
                                        <a href="{sitemap:loc}">
                                            <xsl:value-of select="sitemap:loc"/>
                                        </a>
                                    </span>
                                    <xsl:if test="$has-lastmod">
                                        <span class="lastmod">
                                            <xsl:value-of select="concat(substring(sitemap:lastmod,0,5),  '/', substring(sitemap:lastmod,6,2), '/', substring(sitemap:lastmod,9,2), concat(' ', substring(sitemap:lastmod,12,5)),concat(' ', substring(sitemap:lastmod,20,6)))"/>
                                        </span>
                                    </xsl:if>
                                    <xsl:if test="$has-changefreq">
                                        <span class="changefreq">
                                            <xsl:value-of select="sitemap:changefreq"/>
                                        </span>
                                    </xsl:if>
                                    <xsl:if test="$has-image">
                                        <span class="image">
                                            <xsl:value-of select="count(image:image)"/>
                                        </span>
                                    </xsl:if>
                                </li>
                            </xsl:for-each>
                        </ul>
                    </xsl:if>
                </div>
            </div>
        </body>
    </html>
    </xsl:template>
</xsl:stylesheet>

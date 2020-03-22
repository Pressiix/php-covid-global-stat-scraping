        <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
        <script>
            
            const proxyurl = "https://cors-anywhere.herokuapp.com/";
            const url = "https://www.worldometers.info/coronavirus/#countries"; // site that doesn’t send Access-Control-*
            fetch(proxyurl + url) // https://cors-anywhere.herokuapp.com/https://example.com
            .then(response => response.text())
            .then(contents => { this.response(contents) })

            var removeElements = function(text, selector) {
                var wrapped = $("<div>" + text + "</div>");
                wrapped.find(selector).remove();
                return wrapped.html();
            }
            
            function response(html)
            {
                //get table element only
                html = html.substring( html.indexOf("<div class=\"main_table_countries_div\">")+38, html.lastIndexOf("</table>")+8 );
                html = removeElements(html, "br");
                this.html2json(html);
            }

            function html2json(html) {
                var myRows = [];
                var $headers = $("th",html);
                var totaldeath_index = '';
                var country = [];
                var new_array = {};
                $headers.each(function(thIndex) {
                    if($(this).html() == 'TotalDeaths')
                    {
                        totaldeath_index = thIndex;
                    }
                });    
                var $rows = $("tbody tr",html).each(function(index) {
                    $cells = $(this).find("td",html);
                    myRows[index] = {};
                    $cells.each(function(cellIndex) {
                        //remove white space in total death field
                        if(cellIndex == totaldeath_index)
                        {
                            myRows[index][$($headers[cellIndex]).html()] = $.trim($(this).html());
                        }
                        else if(cellIndex == 0){
                            //myRows[index][$($headers[cellIndex]).html()] = $(this).text();
                            country[index] = $(this).text();
                        }
                        else{
                            myRows[index][$($headers[cellIndex]).html()] = $(this).html();
                        }
                        
                    });    
                });
                
                $.each(country, function( index, value ) {
                    
                    new_array[value] = myRows[index];
                });

                var json = new_array;
                json = JSON.stringify(json);
                
                // Fire off the request to /form.php
                request = $.ajax({
                    url: "store",
                    type: "post",
                    data: { json : json }
                });

                // Callback handler that will be called on success
                request.done(function (response){
                     // Log the response to the console
                    console.log("Response: "+response);
                    $("body").append(response);
                });
            }
        </script>
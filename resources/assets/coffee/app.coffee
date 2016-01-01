do ($ = jQuery, window, document) ->
    csrfToken = $ "meta[name='csrf-token']"
        .attr "content"

    $.ajaxSetup
        headers:
            'X-CSRF-TOKEN': csrfToken

    shouldSkipCommands = ["artisan tinker", "mysql", "tinker"]
    executableCommands = ["mysql"]
    $.each Terminal.endPoints, (key, url) ->
        if $.inArray(key, shouldSkipCommands) is -1
            executableCommands.push key

    executableCommands.sort()

    showAvailableCommands = ->
        temp = ["help", "clear"].concat executableCommands
        temp.sort()
        availableCommands = []
        $.each temp, (i, command) ->
            availableCommands.push info(command)
        "#{comment('Available commands:')} #{availableCommands.join(", ")}"

    outputFormater = (str, color) ->
        str = str.replace("[", "&#91;").replace("]", "&#93;")
        "[[;#{color};]#{str}]"

    info = (str) ->
        outputFormater str, "#008400"

    comment = (str) ->
        outputFormater str, "#a50"

    greetings =
        copyright: [
            " __                        _    _____              _         _ "
            "|  |   ___ ___ ___ _ _ ___| |  |_   ____ ___ _____|_|___ ___| |"
            "|  |__| .'|  _| .'| | | -_| |    | || -_|  _|     | |   | .'| |"
            "|_____|__,|_| |__,|\\_/|___|_|    |_||___|_| |_|_|_|_|_|_|__,|_|"
            ""
            "Copyright (c) 2015 Recca Tsai <http://phpwrite.blogspot.tw/>"
            ""
            ""
            ""
            "Type a command, or type `#{info('help')}`, for a list of commands."
            ""
            "#{showAvailableCommands()}"
            ""

        ].join "\n"


        production: [
            ""
            comment("**************************************")
            comment("*     Application In Production!     *")
            comment("**************************************")
            ""
        ].join "\n"

    request = do ->
        ids = {}
        (url, term, method, params) ->

            ids[url] = ids[url] || 0;
            term.pause()
            $.ajax
                url: url,
                dataType: 'json'
                type: 'post'
                data:
                    jsonrpc: "2.0"
                    method: method
                    params: params
                    id: ++ids[url]
            .success (response) ->
                if response.result is false
                    term.error response.error
                else
                    term.echo response.result
            .error (xhr, type, message) ->
                term.error message
            .complete ->
                term.resume()

    terminalConfirm = do ->
        parseBoolean = (result) ->
            return switch (result.toLowerCase())
                when 'y', 'yes' then true
                else false

        (term, message = "", prompt = ">") ->
            defer = $.Deferred()

            if message isnt ""
                term.echo message

            term.echo "#{prompt}\n>"
            term.push (command) ->
                defer.resolve parseBoolean(command)
                term.pop()
                return
            ,
                prompt: ""
            defer.promise()

    starts_with = (str, search) ->
        return str.indexOf(search) is 0

    interpreter = (command, term, callback =(() ->), prompt) ->
        unless prompt
            prompt = command

        term.push (command) ->
            callback(prompt, command, term)
            return false
        ,
            prompt: "#{prompt}>"


        return false

    execute = (cmd, term) ->
        endPoint = Terminal.endPoints[cmd.name]
        params = cmd.args
        method = params.shift() or []

        if (cmd.name is "artisan" and Terminal.environment is "production" and $.inArray("--force", params) is -1) and (
            (starts_with(method, "migrate") is true and starts_with(method, "migrate:status") is false) or
            starts_with(method, "db:seed") is true
        )
            terminalConfirm term, "#{greetings.production}", "#{info('Do you really wish to run this command? [y/N] (yes/no)')} &#91;#{comment('no')}&#93;: "
                .done (result) ->
                    if result is true
                        params.push "--force"
                        request endPoint, term, method, params
                    else
                        term.echo "\n#{comment('Command Cancelled!')}"
            return
        else
            request endPoint, term, method, params
        return

    $(document.body).terminal (command, term) ->
        switch command
            when "help"
                term.echo " "
                term.echo showAvailableCommands()
                term.echo " "
                return
            when ""
                return
            when "mysql"
                interpreter command, term, (prompt, command, term) ->
                    endPoint = Terminal.endPoints[prompt]
                    request endPoint, term, command, []
                return
            when "artisan tinker"
                interpreter command, term, (prompt, command, term) ->
                    endPoint = Terminal.endPoints[prompt]
                    request endPoint, term, command, []
                , "tinker"
                return
            else
                cmd = $.terminal.parseCommand command.trim()
                if $.inArray(cmd.name, executableCommands) isnt -1
                    execute cmd, term
                    return
                else
                    term.error "Command '#{command}' Not Found!"
                    return
    ,
        greetings: greetings.copyright
        onBlur: ->
            false

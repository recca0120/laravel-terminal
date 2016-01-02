do ($ = jQuery, window, document) ->
    csrfToken = $ "meta[name='csrf-token']"
        .attr "content"

    $.ajaxSetup
        headers:
            'X-CSRF-TOKEN': csrfToken

    outputFormater = (str, color) ->
        str = str
            .replace /&/g, "&amp;"
            .replace /\[/g, "&#91;"
            .replace /\]/g, "&#93;"
        "[[;#{color};]#{str}]"

    info = (str) ->
        outputFormater str, "#008400"

    comment = (str) ->
        outputFormater str, "#a50"

    environment = window.Terminal.environment
    endPoint = window.Terminal.endPoint
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
            ""
        ].join "\n"

        production: [
            ""
            comment("**************************************")
            comment("*     Application In Production!     *")
            comment("**************************************")
            ""
        ].join "\n"



    Loading = do ->
        anim = ["/", "|", "\\", "-"]
        intervalId = null
        delay = 50
        defaultPrompt = null
        show: (term) ->
            term.disable()
            defaultPrompt = term.get_prompt()
            i = 0
            intervalId = setInterval ->
                term.set_prompt anim[i++]
                if i > anim.length-1
                    i = 0
            , delay
        hide: (term) ->
            clearInterval intervalId
            term.enable()
            term.set_prompt defaultPrompt


    class Term
        ids: {},
        toBoolean: (result) =>
            return switch (result.toLowerCase())
                when 'y', 'yes' then true
                else false
        confirm: (message, title) =>
            deferred = $.Deferred()
            term.echo message
            term.echo title
            term.push (result) =>
                deferred.resolve @toBoolean result
                term.pop()
                return
            ,
                prompt: ">"
            deferred.promise()
        interpreter: (prompt, term, callback =(() =>)) =>
            term.push (command) =>
                @execute term, "#{prompt.replace(/(\s|_|-)/g,'-')} #{command}"
                return false
            ,
                prompt: "#{prompt}>"
            return false
        rpcRequest: (term, command) =>
            cmd = $.terminal.parseCommand command.trim()
            @ids[cmd.method] = @ids[cmd.method] || 0;
            Loading.show term
            $.ajax
                url: endPoint,
                dataType: 'json'
                type: 'post'
                data:
                    jsonrpc: "2.0"
                    id: ++@ids[cmd.method]
                    cmd: cmd
            .success (response) =>
                term.echo response.result
            .error (jqXhr, json, errorThrown) ->
                term.error "#{jqXhr.status}: #{errorThrown}"
            .complete ->
                Loading.hide term

        execute: (term, command) =>
            switch command
                when "artisan tinker"
                    @interpreter command, term
                else
                    @rpcRequest term, command
            # console.log command
            return

    terminal = new Term
    $(document.body).terminal (command, term) ->
        terminal.execute(term, command)
    ,
        greetings: greetings.copyright
        onBlur: ->
            false



    # shouldSkipCommands = ["artisan tinker", "mysql", "tinker"]
    # executableCommands = ["mysql"]
    # $.each Terminal.endPoints, (key, url) ->
    #     if $.inArray(key, shouldSkipCommands) is -1
    #         executableCommands.push key
    # executableCommands.sort()

    # showAvailableCommands = ->
    #     temp = ["help", "clear"].concat executableCommands
    #     temp.sort()
    #     availableCommands = []
    #     $.each temp, (i, command) ->
    #         availableCommands.push info(command)
    #     "#{comment('Available commands:')} #{availableCommands.join(", ")}"



    # info = (str) ->
    #     outputFormater str, "#008400"

    # comment = (str) ->
    #     outputFormater str, "#a50"

    # Loading = do ->
    #     anim = ["/", "|", "\\", "-"]
    #     intervalId = null
    #     delay = 50
    #     defaultPrompt = null
    #     show: (term) ->
    #         term.disable()
    #         defaultPrompt = term.get_prompt()
    #         i = 0
    #         intervalId = setInterval ->
    #             term.set_prompt anim[i++]
    #             if i > anim.length-1
    #                 i = 0
    #         , delay
    #     hide: (term) ->
    #         clearInterval intervalId
    #         term.enable()
    #         term.set_prompt defaultPrompt


    # greetings =
    #     copyright: [
    #         " __                        _    _____              _         _ "
    #         "|  |   ___ ___ ___ _ _ ___| |  |_   ____ ___ _____|_|___ ___| |"
    #         "|  |__| .'|  _| .'| | | -_| |    | || -_|  _|     | |   | .'| |"
    #         "|_____|__,|_| |__,|\\_/|___|_|    |_||___|_| |_|_|_|_|_|_|__,|_|"
    #         ""
    #         "Copyright (c) 2015 Recca Tsai <http://phpwrite.blogspot.tw/>"
    #         ""
    #         ""
    #         ""
    #         "Type a command, or type `#{info('help')}`, for a list of commands."
    #         ""
    #         "#{showAvailableCommands()}"
    #         ""
    #     ].join "\n"

    #     production: [
    #         ""
    #         comment("**************************************")
    #         comment("*     Application In Production!     *")
    #         comment("**************************************")
    #         ""
    #     ].join "\n"

    # request = do ->
    #     ids = {}
    #     (url, term, method, params) ->
    #         ids[url] = ids[url] || 0;
    #         # term.pause()
    #         Loading.show term
    #         $.ajax
    #             url: url,
    #             dataType: 'json'
    #             type: 'post'
    #             data:
    #                 jsonrpc: "2.0"
    #                 method: method
    #                 params: params
    #                 id: ++ids[url]
    #         .success (response) ->
    #             if response.result is false
    #                 term.error response.error
    #             else
    #                 term.echo response.result
    #         .error (xhr, type, message) ->
    #             term.error message
    #         .complete ->
    #             Loading.hide term
    #             # term.resume()

    # terminalConfirm = do ->
    #     parseBoolean = (result) ->
    #         return switch (result.toLowerCase())
    #             when 'y', 'yes' then true
    #             else false

    #     (term, message = "", prompt = ">") ->
    #         defer = $.Deferred()

    #         if message isnt ""
    #             term.echo message

    #         term.echo "#{prompt}"
    #         term.push (command) ->
    #             defer.resolve parseBoolean(command)
    #             term.pop()
    #             return
    #         ,
    #             prompt: ">"

    #         defer.promise()

    # starts_with = (str, search) ->
    #     return str.indexOf(search) is 0

    # interpreter = (command, term, callback =(() ->), prompt) ->
    #     unless prompt
    #         prompt = command

    #     term.push (command) ->
    #         callback(prompt, command, term)
    #         return false
    #     ,
    #         prompt: "#{prompt}>"
    #     return false

    # execute = (cmd, term) ->
    #     endPoint = Terminal.endPoints[cmd.name]
    #     params = cmd.args
    #     method = params.shift() or []

    #     if (cmd.name is "artisan" and Terminal.environment is "production" and $.inArray("--force", params) is -1) and (
    #         (starts_with(method, "migrate") is true and starts_with(method, "migrate:status") is false) or
    #         starts_with(method, "db:seed") is true
    #     )
    #         terminalConfirm term, "#{greetings.production}", "#{info('Do you really wish to run this command? [y/N] (yes/no)')} &#91;#{comment('no')}&#93;: "
    #             .done (result) ->
    #                 if result is true
    #                     params.push "--force"
    #                     request endPoint, term, method, params
    #                 else
    #                     term.echo "\n#{comment('Command Cancelled!')}\n"
    #         return
    #     else
    #         request endPoint, term, method, params
    #     return

    # $(document.body).terminal (command, term) ->
    #     switch command
    #         when "help"
    #             term.echo " "
    #             term.echo showAvailableCommands()
    #             term.echo " "
    #             return
    #         when "test"
    #             window.term = term
    #             return
    #         when ""
    #             return
    #         when "mysql"
    #             interpreter command, term, (prompt, command, term) ->
    #                 endPoint = Terminal.endPoints[prompt]
    #                 request endPoint, term, command, []
    #             return
    #         when "artisan tinker"
    #             interpreter command, term, (prompt, command, term) ->
    #                 endPoint = Terminal.endPoints[prompt]
    #                 request endPoint, term, command, []
    #             , "tinker"
    #             return
    #         else
    #             cmd = $.terminal.parseCommand command.trim()
    #             if $.inArray(cmd.name, executableCommands) isnt -1
    #                 execute cmd, term
    #                 return
    #             else
    #                 term.error "Command '#{command}' Not Found!"
    #                 return
    # ,
    #     greetings: greetings.copyright
    #     onBlur: ->
    #         false

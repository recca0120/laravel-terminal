do ($ = jQuery, window, document) ->
    csrfToken = $ "meta[name='csrf-token']"
        .attr "content"

    $.ajaxSetup
        headers:
            'X-CSRF-TOKEN': csrfToken

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

    class OutputFormatterStyle

        colorList:
            30: 'black',
            31: 'red',
            32: 'green',
            33: 'yellow',
            34: 'blue',
            35: 'magenta',
            36: 'cyan',
            37: 'white',

            39: 'white'
        backgroundList:
            40: 'black',
            41: 'red',
            42: 'green',
            43: 'yellow',
            44: 'blue',
            45: 'magenta',
            46: 'cyan',
            47: 'white',

            49: 'black'
        # normal, faited, bold
        colors: $.extend {}, $.terminal.ansi_colors.bold,
            white: $.terminal.ansi_colors.normal.white
            red: $.terminal.ansi_colors.normal.red

        constructor: (foreground = "white", background = "black", @options = []) ->
            @foreground = @ansi foreground
            @background = @ansi background

        is: (foreground = "white", background = "black", @options = []) =>
            return foreground is @foreground and background is @background

        ansi: (color) =>
            if @colors[color]
                return @colors[color]
            else
                return color

        apply: (text) =>
            text = $.terminal.escape_brackets text
            "[[;#{@foreground};#{@background}]#{text}]"

    class OutputFormatter
        formatters:
            error: new OutputFormatterStyle "white", "red"
            info: new OutputFormatterStyle "green"
            comment: new OutputFormatterStyle "yellow"
            question: new OutputFormatterStyle "black"

        error: (text) =>
            @formatters.error.apply text

        info: (text) =>
            @formatters.info.apply text

        comment: (text) =>
            @formatters.comment.apply text

        question: (text) =>
            @formatters.question.apply text

        apply: (text, term) =>
            re = new RegExp "(\\033\\[(\\d+)(;\\d+)?m(((?!\\033\\[\\d+).)*)\\033\\[(\\d+)(;\\d+)?m)|(\\[|\\])", "g"
            text = text
                .replace re, =>
                    m = arguments
                    return switch
                        when $.inArray(m[0], ["[", "]"]) isnt -1
                            $.terminal.escape_brackets m[0]
                        else
                            content = $.terminal.escape_brackets m[4]
                            return switch
                                when m[2] is "32"
                                    @info content
                                when m[2] is "33"
                                    @comment content
                                when m[2] is "37"
                                    @error content
                                else
                                    m[0]
            if term
                $.each text.split("\n"), (i, line) =>
                    if line is ""
                        line = " "
                    term.echo line
            text

    formatter = new OutputFormatter;

    class @Term
        ids: {},
        colors: $.terminal.ansi_colors
        color: (color, background, type = "bold") =>
            if @colors[type] and @colors[type][color]
                return @colors[type][color]
            else
                return color

        error: (text) =>
            formatter.error text

        info: (text) =>
            formatter.info text

        comment: (text) =>
            formatter.comment text

        question: (text) =>
            formatter.question text

        confirm: (term, message, title) =>
            deferred = $.Deferred()
            term.echo title
            term.echo message
            term.push (result) =>
                deferred.resolve @toBoolean result
                term.pop()
                return
            ,
                prompt: ">"
            deferred.promise()

        toBoolean: (result) =>
            return switch (result.toLowerCase())
                when 'y', 'yes' then true
                else false

        interpreter: (commandPrefix, term, prompt) =>
            unless prompt
                prompt = commandPrefix
            term.push (command) =>
                command = command.trim()
                if command
                    @execute term, "#{commandPrefix.replace(/\s+/g, '-')} #{command}"
                return false
            ,
                prompt: "#{prompt}>"
            return false
        rpcRequest: (term, cmd) =>
            @ids[cmd.method] = @ids[cmd.method] || 0;
            Loading.show term
            $.ajax
                url: @options.endPoint,
                dataType: 'json'
                type: 'post'
                data:
                    jsonrpc: "2.0"
                    id: ++@ids[cmd.method]
                    cmd: cmd
            .success (response) =>
                formatter.apply(response.result, term)
            .error (jqXhr, json, errorThrown) ->
                formatter.error("#{jqXhr.status}: #{errorThrown}", term)
            .complete ->
                Loading.hide term

        capitalize = (str) =>
            "#{str.charAt(0).toUpperCase()}#{str.slice(1)}"

        commandArtisan: (term, cmd) =>
            cmd2 = $.terminal.parseCommand cmd.rest.trim()
            title = [
                ""
                @comment("**************************************")
                @comment("*     Application In Production!     *")
                @comment("**************************************")
                ""
            ].join "\n"
            message = "#{@info('Do you really wish to run this command? [y/N] (yes/no)')} #{@comment('[no]')}: "

            if @options.environment is "production" and $.inArray("--force", cmd2.args) is -1
                if (cmd2.name.indexOf("migrate") is 0 and cmd2.name.indexOf("migrate:status") is -1) or cmd2.name.indexOf("db:seed") is 0
                    @confirm term, message, title
                        .done (result) =>
                            if result is true
                                @rpcRequest term, cmd
                            else
                                term.echo " "
                                term.echo "#{@comment('Command Cancelled!')}"
                                term.echo " "
                    return
            @rpcRequest term, cmd

        interpreters:
            "mysql": "mysql"
            "artisan tinker": "tinker"
            "tinker": "tinker"

        execute: (term, command) =>
            command = command.trim()
            switch command
                when "help"
                    cmd = $.terminal.parseCommand "list"
                    @rpcRequest term, cmd
                when ""
                    return
                else
                    for interpreter, prompt of @interpreters
                        if command is interpreter
                            @interpreter prompt, term
                            return

                    cmd = $.terminal.parseCommand command.trim()
                    if @["command#{capitalize(cmd.name)}"]
                        @["command#{capitalize(cmd.name)}"](term, cmd)
                    else
                        @rpcRequest term, cmd
            return

        greetings: =>
            [
                " __                        _    _____              _         _ "
                "|  |   ___ ___ ___ _ _ ___| |  |_   ____ ___ _____|_|___ ___| |"
                "|  |__| .'|  _| .'| | | -_| |    | || -_|  _|     | |   | .'| |"
                "|_____|__,|_| |__,|\\_/|___|_|    |_||___|_| |_|_|_|_|_|_|__,|_|"
                ""
                "Copyright (c) 2015 Recca Tsai <http://phpwrite.blogspot.tw/>"
                ""
                "Type a command, or type `#{@info('help')}`, for a list of commands."
                ""
            ].join "\n"

        constructor: (element, @options)->
            $(element).terminal (command, term) =>
                @execute(term, command)
            ,
                onInit: (term) =>
                    @execute term, 'list'
                onBlur: =>
                    false
                greetings: @greetings()

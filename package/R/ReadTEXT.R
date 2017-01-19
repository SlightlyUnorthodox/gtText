# Title: ReadTEXT.R
#
# Authors:
#   Dax Gerts (gerts.dax@gmail.com)
# Created: 1/19/2017
#
# Description:
#   ReadTEXT serves as an R front-end to the TEXTReader grokit GI. It's purposes is to provide
#   a way to dynamically read and format text data sources into a tabular format usable with the
#   base Grokit library as well as other gtText functions. For further references, the design of
#   ReadTEXT is loosely based off of the construction of ReadCSV.R found in gtBase.
#
#   ReadTEXT employs regular expressions with the
#
# Parameters:
#   files: character vector of raw text data files to be read. Uses absolute or relative paths to current working session.
#   col.name: Column name for delimited data returned by ReadTEXT (default = 'text')
#   delim: Delimiter string used to identify breaks in raw text. (default = '')
#   regex: Specifies whether to interpret delimiter string as a php-style regular expression. (default = FALSE)
#   skip: (from ReadCSV)
#   nrows: (from READCSV)
#   escape:
#   trim.cr:
#   

ReadTEXT <- function(files, debug = 0, col.name = 'text', delim = '\n', regex = FALSE, skip = 0, 
                        nrows = -1, escape = "\\", trim.cr = FALSE, line.number = FALSE) {


    # Check assertions for 'files' (boilerplate adapted from input.R in gtBase)
    assert(is.character(files) && length(files) > 0,
        "'files' should be a character vector specifying the file path(s).")
    assert(all(present <- file_test("-f", files)),
         "missing files:\n", paste(files[!present], collapse = "\n"))
    files <- normalizePath(files)

    # Check assertions for 'col.name'
    assert(is.character(col.name) && length(col.name) > 0,
        "'col.name' should be a string of length greater than '0'".)
    
    # Check assertions for 'delim'
    assert(is.character(delim) && length(delim) > 0 && nchars(delim) >= 1,
        "'delim' should be a non-empty string indicating delimiter value or pattern.")

    # Check assertions for 'regex'
    assert(is.logical(regex) && length(regex) == 1,
        "'regex' should be either 'TRUE' or 'FALSE'")
    
    # If regex == FALSE reformat string as readable regex using capture group
    if regex == FALSE:
        delim = paste(c("/\\", as.character(delim), "\\b/i"), sep = "", collapse = "")
    
    # Check assertions for 'skip'
    assert(is.numeric(skip) && length(skip) == 1 && skip >= 0 && skip == floor(skip),
         "'skip' should be a single non-negative integer.")

    # Check assertions for 'nrows'
    assert(is.numeric(nrows) && length(nrows) == 1 && nrows == floor(nrows),
         "'nrows' should be a single integer.")
    
    # Check assertions for 'escape'
    assert(is.character(escape) && length(escape) == 1 && nchar(escape) >= 1,
         "'escape'should be a single one-character string.")

    # Check assertions for 'trim.cr'
    assert(is.logical(trim.cr) && length(trim.cr) == 1,
        "'trim.cr' should either be 'TRUE' or 'FALSE'")

    # Check assertions for line.number'
    assert(is.logical(line.number) && length(line.number) == 1,
        "'line.number' should be either 'TRUE' or 'FALSE'")

    # Create alias
    alias <- create.alias("read")

    # Initialize GI and run
    gi <- GI(text:TEXTReader, debug, delim, skip, nrows, escape, trim.cr, line.number)

    # Assign attributes
    attributes <- setNames(convert.types(quote(base::STRING)), as.character(col.name))
    if (line.number == TRUE) {
        attributes <- c(setNames(convert.types(quote(base::UInt)), 'line.number'), attributes)
    }

    Input(files, gi, attributes)
}
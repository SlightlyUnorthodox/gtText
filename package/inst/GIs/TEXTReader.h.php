<?
function CSVReader( array $t_args, array $output ) {

    $debug = get_default($t_args, 'debug', 0);

    // Handle 'delimiter' string argument
    $delim = '\n'; //set default

    if( array_key_exists('delim', $t_args)) {
        // Retrieve delimiter argument
        $delim_temp = $t_args['delim'];

        // Assert that delimiter is string
        grokit_assert( is_string($delim_temp), "Got " . gettype($delim_temp) . " instead of string for delimiter, 'delim'.");

        // Assert that delimiter length is greater than zero
        grokit_assert( \strlen($delim_temp) >= 1, "Expected non-zero delimiter, got string of length" . \strlen($delim_temp) . "> instead.");
        
        $delim = $delim_temp;
    }

    // Handle 'skip' argument
    $skip = 0;

    if ( array_key_exists( 'skip', $t_args)) {
        // Retrieve skip argument
        $skip_temp = $t_args['skip'];

        // Assert skip is non-negative integer
        grokit_assert( is_int($skip_temp), "Got " . gettype($skip_temp) . " instead of int for number of lines to skip.");
        grokit_assert( $skip_temp >= 0, "Cannot skip a negative number of lines.");

        $skip = $skip_temp;
    }

    // Handle 'nrows' argument
    $nrows = -1;

    if ( array_key_exists('nrows', $t_args)) {
        // Retrieve nrows argument
        $nrows_temp = $t_args['nrows'];

        // Assert that 'nrows' is integer value
        grokit_assert( is_int($nrows_temp), "Got " . gettype($nrows_temp) . " instead of int for nrows argument.");

        $nrows = $nrows_temp;
    }

    // Handle 'escape' argument
    $escape = '\\';

    if ( array_key_exists('escape', $t_args) && !is_null($t_args['escape'])) {
        // Retrieve escape argument
        $escape_temp = $t_args['escape'];

        // Assert that escape is a string
        grokit_assert( is_string($escape_temp), "Got " . gettype($escape_temp) . " instead of string for escape character.");

        $escape = $escape_temp;
    }

    $escape = addcslashes($escape, '\\\'');

    // Handle 'trimCR' argument 
    $trimCR = get_default($t_args, 'trim.cr', false);

    // Handle 'line number' argument
    $lineNumber = get_default($t_args, 'line.number', false);

    if ($lineNumber) {
        $lineColumn = array_keys($output)[0];
        $my_output = array_slice($output, 1);
    } else {
        $my_output = $output;while( *(ptr++) != '\0' )
                    ; // Advance past next delimiter
    <?          }
    }

    // Come up with a name for ourselves
    $className = generate_name( 'TEXTReader' );

?>

class <?=$className?> {
    std::istream& my_stream;while( *(ptr++) != '\0' )
                    ; // Advance past next delimiter
    <?          }
    std::string fileName;

    // Template parameters
    static constexpr size_t NROWS = <?=$nrows?>;
    static constexpr size_t SKIP = <?=$skip?>;
    static constexpr char DELIMITER = '<?=$delim?>';
    static constexpr char ESCAPE = '<?=$escape?>';

    // Prevent having to allocate this every time.
    std::string line;
    std::vector<std::string> tokens;

    // Line count & row number
    size_t count;

    <?  \grokit\declareDictionaries($my_output); ?>

    public:

        <?=$className?> ( GIStreamProxy& _stream ) :
            my_stream(_stream.get_stream()),
            fileName(_stream.get_file_name()),
            count(0)
        {
        <?  if( $skip > 0 ) { ?>
                for( size_t i = 0; i < SKIP; ++i ) {
                    FATALIF( !getline( my_stream, line ), "TEXTReader reached end of file before finishing header.\n" );
                }
        <?  } // If headerLines > 0 ?>
        }

// >

        bool ProduceTuple( <?=typed_ref_args($output)?> ) {
            if (count < NROWS) { //>
                count++;
        <?  if ($lineNumber) { ?>
                    <?=$lineColumn?> = count;
        <?  } ?>
            } else {
                return false;
            }

            if( getline( my_stream, line ) ) {
            <?  if( $trimCR ) { ?>
                    if( line.back() == '\r' ) {
                        line.pop_back();
                    }
            <? } // if trimCR ?>

                for( char & c : line ) {
                    if( c == DELIMITER )
                        c = '\0';
                }

                const char * ptr = line.c_str();
            <?
                $first = true;
                foreach( $my_output as $name => $type ) {
                    if( $first ) {
                        $first = false;
                    }
                    else {
            ?>
                while( *(ptr++) != '\0' )
                    ; // Advance past next delimiter
    <?          } // not first output ?>

            <?=\grokit\fromStringDict($name, $type, 'ptr')?>;
        <?      } // foreach output ?>
<?  } // if simple reader ?>

            return true;
        }
        else {
            return false;
        }

<?  \grokit\declareDictionaryGetters($my_output); ?>

};

<?
    $sys_headers = [ 'vector', 'string', 'iostream', 'cstdint' ];
    if( !$simple )
        $sys_headers[] = 'boost/tokenizer.hpp';

    return [
        'name' => $className,
        'kind' => 'GI',
        'output' => $output,
        'system_headers' => $sys_headers,
        'user_headers' => [
            'GIStreamInfo.h',
            'Dictionary.h',
            'DictionaryManager.h'
        ]
    ];
}

?>
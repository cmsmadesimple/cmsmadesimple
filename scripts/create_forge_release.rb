#!/usr/bin/env /var/www/cmsmadesimple.org/dev/current/script/runner

require 'tempfile'
require 'mime/types'

basedir = "."
package = ''
stable_package = 1
unstable_package = 26
tr_stable_package = 618
tr_unstable_package = 26

class UploadedFile
  # The filename, *not* including the path, of the "uploaded" file
  attr_reader :original_filename

  # The content type of the "uploaded" file
  attr_accessor :content_type
  
  # Is a lang pack file
  attr_accessor :lang_pack

  def initialize(path, content_type = Mime::TEXT, binary = false)
    raise "#{path} file does not exist" unless File.exist?(path)
    @content_type = content_type
    @original_filename = path.sub(/^.*#{File::SEPARATOR}([^#{File::SEPARATOR}]+)$/) { $1 }
    @tempfile = Tempfile.new(@original_filename)
    @tempfile.set_encoding(Encoding::BINARY) if @tempfile.respond_to?(:set_encoding)
    @tempfile.binmode if binary
    @lang_pack = @original_filename =~ /.*langpack.*/ ? true : false
    FileUtils.copy_file(path, @tempfile.path)
  end

  def path #:nodoc:
    @tempfile.path
  end

  alias local_path path

  def method_missing(method_name, *args, &block) #:nodoc:
    @tempfile.send!(method_name, *args, &block)
  end
end

version_num = ''

if [2, 3].include?(ARGV.size) and ['stable', 'unstable'].include?(ARGV[0])
  package = ARGV[0]
  version_num = ARGV[1]
  basedir = ARGV[2] unless ARGV[2].nil?
else
  puts "Usage: create_forge_release.rb <stable|unstable> <release_number> [full path to directory of files]"
  exit
end

filelist = []
has_lang_files = false

#Loop through every file in the given directory
#Convert them to tempfiles in the format that attachment_fu will like
Dir.chdir(basedir)
Dir.new(basedir).entries.each do |entry|
  if File.file?(entry)
    file = UploadedFile.new(entry, MIME::Types.type_for(entry))
    unless file.nil?
      if file.lang_pack
        has_lang_files = true
      end
      if file.content_type.blank?
        file.content_type = 'text/plain'
      end
      filelist << file
    end
  end
end

#Assuming we have some files, run through the process of creating the release
if filelist.empty?
  puts "No files found!"
  exit
else
  puts "Version #: " + version_num
  puts "Package: " + package
  
  puts "\nCore files to release:"
  filelist.each do |entry|
    if !entry.lang_pack
      puts entry.original_filename
    end
  end
  
  puts "\nLang pack files to release:"
  filelist.each do |entry|
    if entry.lang_pack
      puts entry.original_filename
    end
  end
  
  puts "\nContinue? [y]: "
  the_input = STDIN.gets
  the_input.chomp!
  
  if the_input == '' or the_input == 'y'
    #See if the release already exists before creating a new one
    #If not, then create it
    package_id = package == 'stable' ? stable_package : unstable_package
    release = Release.find_by_package_id_and_name(package_id, version_num)
    if release.nil?
      release = Release.new
      release.package_id = package_id
      release.name = version_num
      release.is_active = true
      unless release.save
        puts "There was an error creating the release object"
        release.errors.each{|attr,msg| puts "#{attr} - #{msg}" }
        exit
      end
    end
    
    tr_release = nil
    if has_lang_files
      tr_package_id = package == 'stable' ? tr_stable_package : tr_unstable_package
      tr_release = Release.find_by_package_id_and_name(tr_package_id, version_num)
      if tr_release.nil?
        tr_release = Release.new
        tr_release.package_id = tr_package_id
        tr_release.name = version_num
        tr_release.is_active = true
        unless tr_release.save
          puts "There was an error creating the release object"
          tr_release.errors.each{|attr,msg| puts "#{attr} - #{msg}" }
          exit
        end
      end
    end
  
    #Now loop through the objects and create the ReleasedFile objects,
    #just as rails would.  This will handle all validations and uploading
    #to S3 automatically.
    filelist.each do |entry|
      released_file = release.released_files.new
      if entry.lang_pack
        released_file = tr_release.released_files.new
      end
      released_file.uploaded_data = entry
      released_file.downloads = 0
      unless released_file.save
        puts "There was an error creating the released_file object fo file: " + entry.original_filename
        released_file.errors.each{|attr,msg| puts "#{attr} - #{msg}" }
        exit
      end
    end
  else
    puts "Exiting"
    exit
  end
  
end
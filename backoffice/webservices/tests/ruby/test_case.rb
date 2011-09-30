require 'rubygems'

require 'base64'
require 'openssl'
require 'soap/wsdlDriver'
require 'test/unit'

module WCMWebServiceTestCase
    ENCRYPTION_CIPHER = 'RC2-CFB'
    ENCRYPTION_IV     = '1/07/05?'
    ENCRYPTION_KEY    = 'ICM.Is.S0.Good!!'

    EXCLUDED_PROPERTIES = [
        'id',
        'createdAt',
        'createdBy',
        'modifiedAt',
        'modifiedBy',
        'fulltext',
        'properties',
    ]

    SERVICE_HOST = 'localhost'
    SERVICE_PATH = '/~deligiaa/wcm/3/branches/3.2.1/webservices/service.php'

    TEST_OBJECT_XML = {
        'article' => "<article>
            <siteId>1</siteId>
            <channelId></channelId>
            <templateId></templateId>
            <title>Sample Article</title>
            <suptitle>Sample Article Suptitle</suptitle>
            <subtitle>Sample Article Subtitle</subtitle>
            <keywords>sample article</keywords>
            <abstract>This is a sample article.</abstract>
            <text>This is a sample article about nothing.</text>
            <publicationDate></publicationDate>
            <expirationDate></expirationDate>
            <author>John Doe</author>
            <moderationKind>a_priori</moderationKind>
            <contributionState>none</contributionState>
            <ratingCount></ratingCount>
            <ratingTotal></ratingTotal>
            <ratingValue></ratingValue>
            <hitCount></hitCount>
            <source></source>
            <sourceId></sourceId>
            <credits></credits>
            <issue></issue>
            <issueNumber></issueNumber>
            <issueDate></issueDate>
            <firstPage></firstPage>
            <workflowState>online</workflowState>
            <mustGenerate>1</mustGenerate>
            <xmlTags></xmlTags>
            <semanticData></semanticData>
            <id></id>
            <createdAt></createdAt>
            <createdBy></createdBy>
            <modifiedAt></modifiedAt>
            <modifiedBy></modifiedBy>
            <properties></properties>
            <className>article</className>
            <fulltext></fulltext>
            <contributionCount>0</contributionCount>
            <photoId></photoId>
            <links></links>
            <chapters></chapters>
            <chaptersCount></chaptersCount>
            <channelTitle></channelTitle>
        </article>",
    }

    class WCMWebServiceNameValuePair
        attr_accessor :name
        attr_accessor :value

        def initialize(name, value)
            self.name = name
            self.value = value
        end
    end

    def assert_objects_equal(xml1, xml2)
        root1 = REXML::Document.new(xml1).root
        root2 = REXML::Document.new(xml2).root
        
        child1 = nil
        child2 = nil
        
        root1.each do |child1|
            case child1
            when REXML::Text
            else
                unless EXCLUDED_PROPERTIES.include?(child1.name)
                    child2 = root2.elements[child1.xpath]
                    assert_equal child1.text, child2.text
                end
            end
        end
        
        child1 = nil
        child2 = nil
    rescue => error
        if child1.nil? or child2.nil?
            flunk error
        else
            flunk "#{error.message}: #{child1.name}1 vs. #{child2.name}2"
        end
    end

    def create_test_object(wcm_om, object_class = 'article', properties = {})
        object_xml = TEST_OBJECT_XML[object_class].dup
        property_name = nil
        
        unless properties.empty?
            root = REXML::Document.new(object_xml).root
            properties.each do |property_name, property_value|
                root.elements["/#{object_class}/#{property_name}"].text = property_value
            end
            object_xml = root.to_s
            property_name = nil
        end
        
        [wcm_om.createObject(session_token, object_class, object_xml), object_xml]
    rescue => error
        if property_name.nil?
            raise error
        else
            flunk "#{error.message}: #{property_name}"
        end
    end

    def encrypt(string)
        # TODO fix current issues with non-PHP clients
        # @cipher = OpenSSL::Cipher::Cipher.new(ENCRYPTION_CIPHER)
        # @cipher.encrypt
        # @cipher.key = ENCRYPTION_KEY
        # @cipher.iv = ENCRYPTION_IV
        # Base64.encode64(@cipher.update(string) + @cipher.final)
        string
    end

    def flunk(reason)
        case reason
        when Exception
            super("#{reason.message}:\n#{reason.backtrace.join("\n")}")
        else
            super(reason)
        end
    end

    def setup
        @services = {}
    end

    def tear_down
        @services.clear
        @services = nil
    end

    def wcm(service_name = self.class.name.sub(/^WCM/, '').sub(/WebServiceTestCase$/, ''))
        unless @services.has_key?(service_name)
            wsdl = wsdl(service_name)
            service = SOAP::WSDLDriverFactory.new(wsdl).create_rpc_driver
            #service.wiredump_dev = $stderr
            @services[service_name] = service
        end
        @services[service_name]
    end

    def wsdl(service_name)
        "http://#{SERVICE_HOST}#{SERVICE_PATH}?class=wcm#{service_name}WebService&wsdl"
    end
end

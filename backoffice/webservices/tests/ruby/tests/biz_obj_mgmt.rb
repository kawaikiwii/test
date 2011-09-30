require 'rexml/document'

require '../test_case'

class WCMBusinessObjectManagementWebServiceTestCase < Test::Unit::TestCase
  include WCMWebServiceTestCase

  def setup
    super
    @session_token = wcm.login('admin', encrypt('admin'), 'en')
  end

  def tear_down
    unless @session_token.nil?
      wcm.logout(@session_token)
      @session_token = nil
    end
    super
  end

  attr_reader :session_token

  def test_create_object
    object_id, object_xml = create_test_object(wcm)
    assert object_id.to_i != 0
    assert_objects_equal object_xml, object_xml # tests XML validity

    object_xml2 = wcm.getObject(session_token, 'article', object_id)
    assert_objects_equal object_xml, object_xml2
  rescue => error
    flunk error
  ensure
    wcm.deleteObject(session_token, 'article', object_id) if object_id.to_i != 0
  end

  def test_create_object_invalid_session_token
    object_id = wcm.createObject('invalid', 'article', '')
    flunk 'expected exception'
  rescue => error
    assert_equal "Invalid session token 'invalid'.", error.message
  ensure
    wcm.deleteObject(session_token, 'article', object_id) if object_id.to_i != 0
  end

  def test_create_object_invalid_class
    object_id = wcm.createObject(session_token, 'invalid', '')
    flunk 'expected exception'
  rescue => error
    assert_equal "Invalid object class name 'invalid'.", error.message
  ensure
    wcm.deleteObject(session_token, 'article', object_id) if object_id.to_i != 0
  end

  def test_create_object_system_class
    object_id = wcm.createObject(session_token, 'wcmUser', '')
    flunk 'expected exception'
  rescue => error
    assert_equal "Invalid object class name 'wcmUser'.", error.message
  ensure
    wcm.deleteObject(session_token, 'article', object_id) if object_id.to_i != 0
  end

  def test_create_object_invalid_xml
    object_id = wcm.createObject(session_token, 'article', '<article>')
    flunk 'expected exception'
  rescue => error
    assert_equal "Invalid object XML '<article>'.", error.message
  ensure
    wcm.deleteObject(session_token, 'article', object_id) if object_id.to_i != 0
  end

  def test_create_object_class_mismatch
    object_id, object_xml = create_test_object(wcm, 'article', 'className' => 'photo')
    flunk 'expected exception'
  rescue => error
    assert error.message =~ %r(^Invalid object XML )
  ensure
    wcm.deleteObject(session_token, 'article', object_id) if object_id.to_i != 0
  end

  def test_create_object_non_zero_id
    object_id, object_xml = create_test_object(wcm, 'article', 'id' => 999999)
    flunk 'expected exception'
  rescue => error
    assert error.message =~ %r(^Invalid object XML )
  ensure
    wcm.deleteObject(session_token, 'article', object_id) if object_id.to_i != 0
  end

  def test_checkin_object
    object_id, object_xml = create_test_object(wcm)

    object_xml2 = wcm.checkoutObject(session_token, 'article', object_id)
    status = wcm.checkinObject(session_token, 'article', object_id, object_xml2)

    object_xml3 = wcm.getObject(session_token, 'article', object_id)
    assert_objects_equal object_xml, object_xml3
  rescue => error
    flunk error
  ensure
    wcm.deleteObject(session_token, 'article', object_id) if object_id.to_i != 0
  end

  def test_checkin_modified_object
    object_id, object_xml = create_test_object(wcm)

    object_xml2 = wcm.checkoutObject(session_token, 'article', object_id)
    root2 = REXML::Document.new(object_xml2).root
    root2.elements['/article/title'].text = 'Foo'
    status = wcm.checkinObject(session_token, 'article', object_id, root2.to_s)

    object_xml3 = wcm.getObject(session_token, 'article', object_id)
    root3 = REXML::Document.new(object_xml3).root
    assert_equal 'Foo', root3.elements['/article/title'].text
  rescue => error
    flunk error
  ensure
    wcm.deleteObject(session_token, 'article', object_id) if object_id.to_i != 0
  end

  def test_checkin_object_invalid_session_token
    object_id, object_xml = create_test_object(wcm)

    object_xml2 = wcm.checkoutObject(session_token, 'article', object_id)
    status = wcm.checkinObject('invalid', 'article', object_id, object_xml2)
    flunk 'expected exception'
  rescue => error
    assert_equal "Invalid session token 'invalid'.", error.message
  ensure
    status = wcm.undoCheckoutObject(session_token, 'article', object_id) if object_id.to_i != 0 and object_xml2 != nil
    wcm.deleteObject(session_token, 'article', object_id)       if object_id.to_i != 0
  end

  def test_checkin_object_invalid_object_class
    object_id, object_xml = create_test_object(wcm)

    object_xml2 = wcm.checkoutObject(session_token, 'article', object_id)
    status = wcm.checkinObject(session_token, 'invalid', object_id, object_xml2)
    flunk 'expected exception'
  rescue => error
    assert_equal "Invalid object class name 'invalid'.", error.message
  ensure
    status = wcm.undoCheckoutObject(session_token, 'article', object_id) if object_id.to_i != 0 and object_xml2 != nil
    wcm.deleteObject(session_token, 'article', object_id)       if object_id.to_i != 0
  end

  def test_checkin_object_invalid_object_id
    object_id, object_xml = create_test_object(wcm)

    object_xml2 = wcm.checkoutObject(session_token, 'article', object_id)
    status = wcm.checkinObject(session_token, 'article', 0, object_xml2)
    flunk 'expected exception'
  rescue => error
    assert_equal "Invalid object identifier 0.", error.message
  ensure
    status = wcm.undoCheckoutObject(session_token, 'article', object_id) if object_id.to_i != 0 and object_xml2 != nil
    wcm.deleteObject(session_token, 'article', object_id)       if object_id.to_i != 0
  end

  def test_checkout_object
    object_id, object_xml = create_test_object(wcm)

    object_xml2 = wcm.checkoutObject(session_token, 'article', object_id)
    assert_objects_equal object_xml, object_xml2
  rescue => error
    flunk error
  ensure
    status = wcm.undoCheckoutObject(session_token, 'article', object_id) if object_id.to_i != 0 and object_xml2 != nil
    wcm.deleteObject(session_token, 'article', object_id)       if object_id.to_i != 0
  end

  def test_checkout_object_invalid_sesssion_token
    object_id, object_xml = create_test_object(wcm)

    object_xml2 = wcm.checkoutObject('invalid', 'article', object_id)
    flunk 'expected exception'
  rescue => error
    assert_equal "Invalid session token 'invalid'.", error.message
  ensure
    status = wcm.undoCheckoutObject(session_token, 'article', object_id) if object_id.to_i != 0 and object_xml2 != nil
    wcm.deleteObject(session_token, 'article', object_id)       if object_id.to_i != 0
  end

  def test_checkout_object_invalid_object_class
    object_id, object_xml = create_test_object(wcm)

    object_xml2 = wcm.checkoutObject(session_token, 'invalid', object_id)
    flunk 'expected exception'
  rescue => error
    assert_equal "Invalid object class name 'invalid'.", error.message
  ensure
    status = wcm.undoCheckoutObject(session_token, 'article', object_id) if object_id.to_i != 0 and object_xml2 != nil
    wcm.deleteObject(session_token, 'article', object_id)       if object_id.to_i != 0
  end

  def test_checkout_object_invalid_object_id
    object_id, object_xml = create_test_object(wcm)

    object_xml2 = wcm.checkoutObject(session_token, 'article', 0)
    flunk 'expected exception'
  rescue => error
    assert_equal "Invalid object identifier 0.", error.message
  ensure
    status = wcm.undoCheckoutObject(session_token, 'article', object_id) if object_id.to_i != 0 and object_xml2 != nil
    wcm.deleteObject(session_token, 'article', object_id)       if object_id.to_i != 0
  end

  def test_undo_checkout_object
    object_id, object_xml = create_test_object(wcm)

    object_xml2 = wcm.checkoutObject(session_token, 'article', object_id)
    status = wcm.undoCheckoutObject(session_token, 'article', object_id)
  rescue => error
    flunk error
  ensure
    wcm.deleteObject(session_token, 'article', object_id)       if object_id.to_i != 0
  end

  def test_undo_checkout_object_invalid_session_token
    object_id, object_xml = create_test_object(wcm)

    object_xml2 = wcm.checkoutObject(session_token, 'article', object_id)
    status = wcm.undoCheckoutObject('invalid', 'article', object_id)
    flunk 'expected exception'
  rescue => error
    assert_equal "Invalid session token 'invalid'.", error.message
  ensure
    status = wcm.undoCheckoutObject(session_token, 'article', object_id) if object_id.to_i != 0 and object_xml2 != nil
    wcm.deleteObject(session_token, 'article', object_id)       if object_id.to_i != 0
  end

  def test_undo_checkout_object_invalid_object_class
    object_id, object_xml = create_test_object(wcm)

    object_xml2 = wcm.checkoutObject(session_token, 'article', object_id)
    status = wcm.undoCheckoutObject(session_token, 'invalid', object_id)
    flunk 'expected exception'
  rescue => error
    assert_equal "Invalid object class name 'invalid'.", error.message
  ensure
    status = wcm.undoCheckoutObject(session_token, 'article', object_id) if object_id.to_i != 0 and object_xml2 != nil
    wcm.deleteObject(session_token, 'article', object_id)       if object_id.to_i != 0
  end

  def test_undo_checkout_object_invalid_object_id
    object_id, object_xml = create_test_object(wcm)

    object_xml2 = wcm.checkoutObject(session_token, 'article', object_id)
    status = wcm.undoCheckoutObject(session_token, 'article', 0)
    flunk 'expected exception'
  rescue => error
    assert_equal "Invalid object identifier 0.", error.message
  ensure
    status = wcm.undoCheckoutObject(session_token, 'article', object_id) if object_id.to_i != 0 and object_xml2 != nil
    wcm.deleteObject(session_token, 'article', object_id)       if object_id.to_i != 0
  end

  def test_undo_checkout_object_not_checked_out
    object_id, object_xml = create_test_object(wcm)

    status = wcm.undoCheckoutObject(session_token, 'article', object_id)
    flunk 'expected exception'
  rescue => error
    assert_equal "Undo check out object failed for 'article' object #{object_id}.", error.message
  ensure
    wcm.deleteObject(session_token, 'article', object_id)       if object_id.to_i != 0
  end

  def test_lock_object
    object_id, object_xml = create_test_object(wcm)

    status = wcm.lockObject(session_token, 'article', object_id)
  rescue => error
    flunk error
  ensure
    status = wcm.unlockObject(session_token, 'article', object_id) if object_id.to_i != 0 and status
    wcm.deleteObject(session_token, 'article', object_id)       if object_id.to_i != 0
  end

  def test_lock_object_invalid_sesssion_token
    object_id, object_xml = create_test_object(wcm)

    status = wcm.lockObject('invalid', 'article', object_id)
    flunk 'expected exception'
  rescue => error
    assert_equal "Invalid session token 'invalid'.", error.message
  ensure
    status = wcm.unlockObject(session_token, 'article', object_id) if object_id.to_i != 0 and status
    wcm.deleteObject(session_token, 'article', object_id)       if object_id.to_i != 0
  end

  def test_lock_object_invalid_object_class
    object_id, object_xml = create_test_object(wcm)

    status = wcm.lockObject(session_token, 'invalid', object_id)
    flunk 'expected exception'
  rescue => error
    assert_equal "Invalid object class name 'invalid'.", error.message
  ensure
    status = wcm.unlockObject(session_token, 'article', object_id) if object_id.to_i != 0 and status
    wcm.deleteObject(session_token, 'article', object_id)       if object_id.to_i != 0
  end

  def test_lock_object_invalid_object_id
    object_id, object_xml = create_test_object(wcm)

    status = wcm.lockObject(session_token, 'article', 0)
    flunk 'expected exception'
  rescue => error
    assert_equal "Invalid object identifier 0.", error.message
  ensure
    status = wcm.unlockObject(session_token, 'article', object_id) if object_id.to_i != 0 and status
    wcm.deleteObject(session_token, 'article', object_id)       if object_id.to_i != 0
  end

  def test_unlock_object
    object_id, object_xml = create_test_object(wcm)

    object_xml2 = wcm.lockObject(session_token, 'article', object_id)
    status = wcm.unlockObject(session_token, 'article', object_id)
  rescue => error
    flunk error
  ensure
    wcm.deleteObject(session_token, 'article', object_id)       if object_id.to_i != 0
  end

  def test_unlock_object_invalid_session_token
    object_id, object_xml = create_test_object(wcm)

    object_xml2 = wcm.lockObject(session_token, 'article', object_id)
    status = wcm.unlockObject('invalid', 'article', object_id)
    flunk 'expected exception'
  rescue => error
    assert_equal "Invalid session token 'invalid'.", error.message
  ensure
    status = wcm.unlockObject(session_token, 'article', object_id) if object_id.to_i != 0 and object_xml2 != nil
    wcm.deleteObject(session_token, 'article', object_id)       if object_id.to_i != 0
  end

  def test_unlock_object_invalid_object_class
    object_id, object_xml = create_test_object(wcm)

    object_xml2 = wcm.lockObject(session_token, 'article', object_id)
    status = wcm.unlockObject(session_token, 'invalid', object_id)
    flunk 'expected exception'
  rescue => error
    assert_equal "Invalid object class name 'invalid'.", error.message
  ensure
    status = wcm.unlockObject(session_token, 'article', object_id) if object_id.to_i != 0 and object_xml2 != nil
    wcm.deleteObject(session_token, 'article', object_id)       if object_id.to_i != 0
  end

  def test_unlock_object_invalid_object_id
    object_id, object_xml = create_test_object(wcm)

    object_xml2 = wcm.lockObject(session_token, 'article', object_id)
    status = wcm.unlockObject(session_token, 'article', 0)
    flunk 'expected exception'
  rescue => error
    assert_equal "Invalid object identifier 0.", error.message
  ensure
    status = wcm.unlockObject(session_token, 'article', object_id) if object_id.to_i != 0 and object_xml2 != nil
    wcm.deleteObject(session_token, 'article', object_id)       if object_id.to_i != 0
  end

  def test_unlock_object_not_checked_out
    object_id, object_xml = create_test_object(wcm)

    status = wcm.unlockObject(session_token, 'article', object_id)
    flunk 'expected exception'
  rescue => error
    assert_equal "Undo check out object failed for 'article' object #{object_id}.", error.message
  ensure
    wcm.deleteObject(session_token, 'article', object_id) if object_id.to_i != 0
  end

  def test_get_object
    object_id, object_xml = create_test_object(wcm)

    object_xml2 = wcm.getObject(session_token, 'article', object_id)
    assert_objects_equal object_xml, object_xml2
  rescue => error
    flunk error
  ensure
    wcm.deleteObject(session_token, 'article', object_id) if object_id.to_i != 0
  end

  def test_get_object_invalid_session_token
    object_id, object_xml = create_test_object(wcm)

    wcm.getObject('jdkfjdfkjfdkf', 'article', object_id)
    flunk 'expected exception'
  rescue => error
    assert_equal "Invalid session token 'jdkfjdfkjfdkf'.", error.message
  ensure
    wcm.deleteObject(session_token, 'article', object_id) if object_id.to_i != 0
  end

  def test_get_object_invalid_object_class
    object_id, object_xml = create_test_object(wcm)

    wcm.getObject(session_token, 'invalid', object_id)
    flunk 'expected exception'
  rescue => error
    assert_equal "Invalid object class name 'invalid'.", error.message
  ensure
    wcm.deleteObject(session_token, 'article', object_id) if object_id.to_i != 0
  end

  def test_get_object_invalid_object_id
    object_id, object_xml = create_test_object(wcm)

    wcm.getObject(session_token, 'article', 0)
    flunk 'expected exception'
  rescue => error
    assert_equal "Invalid object identifier 0.", error.message
  ensure
    wcm.deleteObject(session_token, 'article', object_id) if object_id.to_i != 0
  end

  def test_get_objects
    object_id1, object_xml1 = create_test_object(wcm)
    object_id2, object_xml2 = create_test_object(wcm)
    object_id3, object_xml3 = create_test_object(wcm)

    object_xmls = wcm.getObjects(session_token, 'article', "id in (#{object_id1}, #{object_id2}, #{object_id3})", '')
    assert_objects_equal object_xml1, object_xmls[0]
    assert_objects_equal object_xml2, object_xmls[1]
    assert_objects_equal object_xml3, object_xmls[2]
  rescue => error
    flunk error
  ensure
    wcm.deleteObject(session_token, 'article', object_id1) if object_id1.to_i != 0
    wcm.deleteObject(session_token, 'article', object_id2) if object_id2.to_i != 0
    wcm.deleteObject(session_token, 'article', object_id3) if object_id3.to_i != 0
  end

  def test_delete_object
    object_id, object_xml = create_test_object(wcm)
    status = wcm.deleteObject(session_token, 'article', object_id)
    assert status
  rescue => error
    flunk error
  end

  def test_delete_object_invalid_session_token
    object_id, object_xml = create_test_object(wcm)
    status = wcm.deleteObject('invalid', 'article', object_id)
    flunk 'expected exception'
  rescue => error
    assert_equal "Invalid session token 'invalid'.", error.message
  ensure
    wcm.deleteObject(session_token, 'article', object_id) unless status
  end

  def test_delete_object_invalid_object_class
    object_id, object_xml = create_test_object(wcm)
    status = wcm.deleteObject(session_token, 'invalid', object_id)
    flunk 'expected exception'
  rescue => error
    assert_equal "Invalid object class name 'invalid'.", error.message
  ensure
    wcm.deleteObject(session_token, 'article', object_id) unless status
  end

  def test_delete_object_invalid_object_id
    object_id, object_xml = create_test_object(wcm)
    status = wcm.deleteObject(session_token, 'article', 0)
    flunk 'expected exception'
  rescue => error
    assert_equal "Invalid object identifier 0.", error.message
  ensure
    wcm.deleteObject(session_token, 'article', object_id) unless status
  end

  def test_delete_object_when_locked
    object_id, object_xml = create_test_object(wcm)
    status1 = wcm.lockObject(session_token, 'article', object_id)
    status2 = wcm.deleteObject(session_token, 'article', object_id)
    flunk 'expected exception'
  rescue => error
    assert_equal "Delete failed for 'article' object #{object_id}.", error.message
  ensure
    wcm.unlockObject(session_token, 'article', object_id) if status1
    wcm.deleteObject(session_token, 'article', object_id) if object_id.to_i != 0
  end

  def test_delete_objects
    object_id1, object_xml1 = create_test_object(wcm)
    object_id2, object_xml2 = create_test_object(wcm)
    object_id3, object_xml3 = create_test_object(wcm)

    where = "id in (#{object_id1}, #{object_id2}, #{object_id3})"

    status = wcm.deleteObjects(session_token, 'article', where)
    assert status

    object_xmls = wcm.getObjects(session_token, 'article', where, '')
    assert object_xmls.empty?
  rescue => error
    flunk error
  end
end

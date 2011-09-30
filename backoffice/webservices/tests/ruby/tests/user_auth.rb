require '../test_case'

class WCMUserAuthenticationWebServiceTestCase < Test::Unit::TestCase
  include WCMWebServiceTestCase

  def test_login_logout
    session_token = wcm.login('admin', encrypt('admin'), 'en')
    assert_not_nil session_token
  rescue => error
    flunk error
  ensure
    wcm.logout(session_token) unless session_token.nil?
  end

  def test_login_invalid_user_id
    session_token = wcm.login('invalid', encrypt('admin'), 'en')
    flunk 'expected exception'
  rescue => error
    assert_equal "Invalid user identifier or password for user 'invalid'.", error.message
  ensure
    wcm.logout(session_token) unless session_token.nil?
  end

  def test_login_invalid_password
    session_token = wcm.login('admin', encrypt('invalid'), 'en')
    flunk 'expected exception'
  rescue => error
    assert_equal "Invalid user identifier or password for user 'admin'.", error.message
  ensure
    wcm.logout(session_token) unless session_token.nil?
  end

  def test_login_invalid_language
    # specifying an invalid language is not a fatal error - the web
    # service will use the language specified in the WCM configuration
    session_token = wcm.login('admin', encrypt('admin'), 'invalid')
    assert_not_nil session_token
  rescue => error
    flunk error
  ensure
    wcm.logout(session_token) unless session_token.nil?
  end

  def test_login_invalid_language_with_error
    # specifying an invalid language is not a fatal error - the web
    # service will use the language specified in the WCM configuration
    #
    # here, we test the localization of the error message associated
    # with a login failure due to a double login with a different user
    session_token = wcm.login('invalid', encrypt('invalid'), 'invalid')
    flunk 'expected exception'
  rescue => error
    assert_equal "Invalid user identifier or password for user 'invalid'.", error.message
  ensure
    wcm.logout(session_token) unless session_token.nil?
  end

  def test_double_login
    # multiple logins with the same credentials is ok
    session_token1 = wcm.login('admin', encrypt('admin'), 'en')
    session_token2 = wcm.login('admin', encrypt('admin'), 'en')
    assert_equal session_token1, session_token2
  rescue => error
    flunk error
  ensure
    wcm.logout(session_token1) unless session_token1.nil?
    wcm.logout(session_token2) unless session_token2.nil? or session_token2 == session_token1
  end

  def test_double_login_different_users
    # multiple logins with the different credentials is not ok
    session_token1 = wcm.login('admin', encrypt('admin'), 'en')
    session_token2 = wcm.login('guest', encrypt('guest'), 'en')
    flunk 'expected exception'
  rescue => error
    assert_equal "Invalid user identifier 'guest' (session already exists with different credentials).", error.message
  ensure
    wcm.logout(session_token1) unless session_token1.nil?
    wcm.logout(session_token2) unless session_token2.nil? or session_token2 == session_token1
  end

  def test_double_login_different_languages
    # multiple logins with the same credentials but different languages is ok
    session_token1 = wcm.login('admin', encrypt('admin'), 'en')
    session_token2 = wcm.login('admin', encrypt('admin'), 'fr')
    assert_equal session_token1, session_token2
  rescue => error
    flunk error
  ensure
    wcm.logout(session_token1) unless session_token1.nil?
    wcm.logout(session_token2) unless session_token2.nil? or session_token2 == session_token1
  end

  def test_double_login_different_languages_with_error
    # multiple logins with the same credentials but different languages is ok
    #
    # here, we test the localization of the error message associated
    # with a login failure due to a double login with a different user
    session_token1 = wcm.login('admin', encrypt('admin'), 'en')
    session_token2 = wcm.login('admin', encrypt('admin'), 'fr')
    session_token3 = wcm.login('guest', encrypt('guest'), 'en')
    flunk 'expected exception'
  rescue => error
    assert_equal "Invalid user identifier 'guest' (session already exists with different credentials).", error.message
  ensure
    wcm.logout(session_token1) unless session_token1.nil?
    wcm.logout(session_token2) unless session_token2.nil? or session_token2 == session_token1
    wcm.logout(session_token3) unless session_token3.nil?
  end

  def test_logout_invalid_session_token
    wcm.logout('kldlkfkdlfdkfldkfdf')
    flunk 'expected exception'
  rescue => error
    assert_equal "Invalid session token 'kldlkfkdlfdkfldkfdf'.", error.message
  end
end
